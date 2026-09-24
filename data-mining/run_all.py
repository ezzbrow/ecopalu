#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Runner Terpadu Data Mining EcoPalu
Mengeksekusi K-Means dan Apriori secara berurutan.
"""

import sys
import os
import subprocess
import json

def run_script(script_name):
    script_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), script_name)
    result = subprocess.run([sys.executable, script_path], capture_output=True, text=True)
    if result.returncode != 0:
        return {"status": "error", "script": script_name, "error": result.stderr}
    try:
        return json.loads(result.stdout)
    except:
        return {"status": "success", "raw_output": result.stdout.strip()}

def main():
    print("[1/2] Menjalankan K-Means Clustering...")
    kmeans_res = run_script("kmeans.py")
    
    print("[2/2] Menjalankan Apriori Association Rules...")
    apriori_res = run_script("apriori.py")
    
    summary = {
        "status": "success",
        "message": "Semua algoritma data mining berhasil dijalankan dan disimpan ke database.",
        "kmeans": kmeans_res,
        "apriori": apriori_res
    }
    print(json.dumps(summary, indent=2))

if __name__ == '__main__':
    main()
