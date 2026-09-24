#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Modul Data Mining: Algoritma Apriori Pola Kombinasi Kategori Sampah
Library: pandas, mlxtend (TransactionEncoder, apriori, association_rules), mysql-connector-python
Alur:
1. Mengambil data transaksi penjemputan dari MySQL, mengelompokkannya per keranjang (user_id + tanggal_jemput).
2. Transformasi transaksi ke format One-Hot Encoding menggunakan mlxtend TransactionEncoder.
3. Menjalankan fungsi apriori(min_support=0.15) dari pustaka mlxtend.
4. Menghasilkan association_rules(metric="confidence", min_threshold=0.45).
5. Menghitung dan memfilter aturan dengan Lift Ratio > 1.0.
6. Menyimpan hasil aturan asosiasi ke tabel 'hasil_asosiasi' di MySQL.
"""

import sys
import os
import json
import warnings
from datetime import datetime
import mysql.connector
import pandas as pd
from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import apriori, association_rules

# Supress pandas / mlxtend deprecation warnings
warnings.filterwarnings('ignore')

DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'ecopalu',
    'port': 3306
}

def get_connection():
    return mysql.connector.connect(**DB_CONFIG)

def fetch_baskets():
    """Mengambil keranjang sampah penjemputan"""
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)
    query = """
    SELECT 
        p.user_id,
        p.tanggal_jemput,
        k.nama_kategori
    FROM penjemputan p
    JOIN kategori_sampah k ON k.id = p.kategori_sampah_id
    WHERE p.status = 'selesai'
    ORDER BY p.user_id, p.tanggal_jemput
    """
    cursor.execute(query)
    rows = cursor.fetchall()
    cursor.close()
    conn.close()

    basket_dict = {}
    for r in rows:
        key = f"{r['user_id']}_{r['tanggal_jemput']}"
        if key not in basket_dict:
            basket_dict[key] = []
        # Normalisasi nama kategori ke tampilan bersih
        item_clean = " ".join([w.capitalize() for w in r['nama_kategori'].split('_')])
        if item_clean not in basket_dict[key]:
            basket_dict[key].append(item_clean)

    return list(basket_dict.values())

def run_mlxtend_apriori(baskets, min_support=0.15, min_confidence=0.45):
    if not baskets or len(baskets) < 2:
        return []

    # 1. One-Hot Encoding dengan mlxtend TransactionEncoder
    te = TransactionEncoder()
    te_ary = te.fit(baskets).transform(baskets)
    df_encoded = pd.DataFrame(te_ary, columns=te.columns_)

    # 2. Frequent Itemsets dengan mlxtend apriori
    frequent_itemsets = apriori(df_encoded, min_support=min_support, use_colnames=True)
    if frequent_itemsets.empty:
        return []

    # 3. Association Rules dengan mlxtend association_rules
    rules_df = association_rules(frequent_itemsets, metric="confidence", min_threshold=min_confidence)
    if rules_df.empty:
        return []

    # Filter lift > 1.0 (korelasi positif)
    rules_df = rules_df[rules_df['lift'] >= 1.0].sort_values(by=['lift', 'confidence'], ascending=[False, False])

    results = []
    for _, row in rules_df.iterrows():
        ante = ", ".join(list(row['antecedents']))
        conseq = ", ".join(list(row['consequents']))
        sup = float(row['support'])
        conf = float(row['confidence'])
        lift = float(row['lift'])

        ket = (
            f"Nasabah yang menyetor [{ante}] memiliki probabilitas {conf*100:.1f}% "
            f"untuk juga menyetor [{conseq}] (Korelasi positif Lift: {lift:.2f}x)."
        )

        results.append({
            'antecedents': ante,
            'consequents': conseq,
            'support': round(sup, 4),
            'confidence': round(conf, 4),
            'lift': round(lift, 4),
            'keterangan': ket
        })

    return results

def save_to_mysql(rules):
    conn = get_connection()
    cursor = conn.cursor()
    cursor.execute("TRUNCATE TABLE hasil_asosiasi")

    insert_sql = """
    INSERT INTO hasil_asosiasi 
    (antecedents, consequents, support, confidence, lift, keterangan, created_at, updated_at)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """

    now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    data = []
    for r in rules:
        data.append((
            r['antecedents'],
            r['consequents'],
            float(r['support']),
            float(r['confidence']),
            float(r['lift']),
            r['keterangan'],
            now,
            now
        ))

    cursor.executemany(insert_sql, data)
    conn.commit()
    cursor.close()
    conn.close()

def main():
    baskets = fetch_baskets()
    if not baskets:
        print(json.dumps({"status": "warning", "message": "Tidak ada data keranjang transaksi penjemputan."}))
        return

    rules = run_mlxtend_apriori(baskets, min_support=0.15, min_confidence=0.45)
    save_to_mysql(rules)

    summary = {
        "status": "success",
        "engine": "pandas + mlxtend (apriori & association_rules)",
        "total_keranjang": len(baskets),
        "total_rules": len(rules),
        "top_rules": rules[:3]
    }
    print(json.dumps(summary, indent=2))

if __name__ == '__main__':
    main()
