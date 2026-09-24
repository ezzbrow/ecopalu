#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Modul Data Mining: K-Means Clustering Nasabah EcoPalu
Library: pandas, scikit-learn (KMeans, MinMaxScaler), mysql-connector-python
Alur:
1. Membaca data transaksi/agregasi nasabah dari MySQL (RFM: Frekuensi, Total Berat, Total Poin, Recency).
2. Data selection, cleaning, & transformasi menggunakan Pandas DataFrame.
3. Normalisasi data menggunakan MinMaxScaler dari scikit-learn.
4. Menjalankan algoritma KMeans(n_clusters=3, random_state=42) dari scikit-learn.
5. Memberikan label klaster dan rekomendasi aksi bisnis operasional.
6. Menyimpan hasil klaster ke tabel 'hasil_klaster' di MySQL.
"""

import sys
import os
import json
from datetime import datetime
import mysql.connector
import pandas as pd
from sklearn.cluster import KMeans
from sklearn.preprocessing import MinMaxScaler

DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'ecopalu',
    'port': 3306
}

def get_connection():
    return mysql.connector.connect(**DB_CONFIG)

def fetch_data_as_df():
    """Mengambil data mentah agregasi nasabah menggunakan Pandas DataFrame"""
    conn = get_connection()
    query = """
    SELECT 
        u.id AS user_id,
        u.name AS nama_nasabah,
        u.email,
        COUNT(p.id) AS frekuensi,
        COALESCE(SUM(p.berat), 0) AS total_berat,
        COALESCE(SUM(p.coin_award), 0) AS total_poin,
        COALESCE(DATEDIFF(CURDATE(), MAX(p.tanggal_jemput)), 0) AS recency_hari
    FROM users u
    JOIN penjemputan p ON p.user_id = u.id AND p.status = 'selesai'
    WHERE u.role = 'user' AND u.deleted_at IS NULL
    GROUP BY u.id, u.name, u.email
    HAVING frekuensi > 0
    ORDER BY total_berat DESC
    """
    df = pd.read_sql(query, conn)
    conn.close()
    return df

def run_clustering(df, n_clusters=3):
    if df.empty or len(df) < n_clusters:
        return df

    # Data Selection: Ambil 4 atribut RFMW
    feature_cols = ['frekuensi', 'total_berat', 'total_poin', 'recency_hari']
    X = df[feature_cols].copy()

    # Transformasi: Recency dibalik agar nilai lebih baru (recency kecil) mendapat skor tinggi
    max_recency = X['recency_hari'].max()
    X['recency_inv'] = max_recency - X['recency_hari']
    norm_features = ['frekuensi', 'total_berat', 'total_poin', 'recency_inv']

    # Normalisasi menggunakan scikit-learn MinMaxScaler
    scaler = MinMaxScaler()
    X_scaled = scaler.fit_transform(X[norm_features])

    # Eksekusi K-Means dari scikit-learn
    kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
    df['cluster_raw'] = kmeans.fit_predict(X_scaled)

    # Identifikasi makna klaster berdasarkan rata-rata total_berat dan frekuensi
    cluster_means = df.groupby('cluster_raw')[['total_berat', 'frekuensi']].mean()
    cluster_ranks = cluster_means.mean(axis=1).sort_values(ascending=False).index.tolist()

    label_map = {}
    rek_map = {}
    if len(cluster_ranks) >= 3:
        label_map[cluster_ranks[0]] = "Nasabah Sangat Aktif (Prioritas)"
        rek_map[cluster_ranks[0]] = "Berikan reward koin bonus & jadwalkan penjemputan prioritas mingguan."

        label_map[cluster_ranks[1]] = "Nasabah Potensial (Sedang)"
        rek_map[cluster_ranks[1]] = "Kirim notifikasi pengingat jadwal penjemputan berkala dan edukasi pemilahan."

        label_map[cluster_ranks[2]] = "Nasabah Pasif (Perlu Reaktivasi)"
        rek_map[cluster_ranks[2]] = "Tawarkan promo double coin untuk setoran berikutnya agar aktif kembali."

    df['cluster_id'] = df['cluster_raw']
    df['cluster_label'] = df['cluster_raw'].map(label_map).fillna("Nasabah Umum")
    df['rekomendasi'] = df['cluster_raw'].map(rek_map).fillna("Monitor aktivitas rutin.")

    return df

def save_to_mysql(df):
    """Menyimpan hasil segmentasi ke tabel MySQL hasil_klaster"""
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("TRUNCATE TABLE hasil_klaster")

    insert_sql = """
    INSERT INTO hasil_klaster 
    (user_id, nama_nasabah, email, frekuensi, total_berat, total_poin, recency_hari, cluster_id, cluster_label, rekomendasi, created_at, updated_at)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """

    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    data = []
    for _, row in df.iterrows():
        data.append((
            int(row['user_id']),
            str(row['nama_nasabah']),
            str(row['email']),
            int(row['frekuensi']),
            float(row['total_berat']),
            int(row['total_poin']),
            int(row['recency_hari']),
            int(row['cluster_id']),
            str(row['cluster_label']),
            str(row['rekomendasi']),
            now,
            now
        ))

    cursor.executemany(insert_sql, data)
    conn.commit()
    cursor.close()
    conn.close()

def main():
    df = fetch_data_as_df()
    if df.empty:
        print(json.dumps({"status": "warning", "message": "Dataset kosong."}))
        return

    n_clusters = min(3, len(df))
    df_clustered = run_clustering(df, n_clusters=n_clusters)
    save_to_mysql(df_clustered)

    summary = {
        "status": "success",
        "engine": "pandas + scikit-learn (KMeans)",
        "total_nasabah": len(df_clustered),
        "k": n_clusters,
        "clusters": df_clustered['cluster_label'].value_counts().to_dict()
    }
    print(json.dumps(summary, indent=2))

if __name__ == '__main__':
    main()
