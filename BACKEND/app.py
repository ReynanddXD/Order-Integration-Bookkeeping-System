from flask import Flask, jsonify, request
from flask_cors import CORS
import pymysql

app = Flask(__name__)
CORS(app)  # supaya bisa diakses dari frontend

# koneksi ke database Laragon
conn = pymysql.connect(
    host='localhost',
    user='root',
    password='',
    db='nama_database_kamu'
)

@app.route('/api/pesanan')
def get_pesanan():
    with conn.cursor() as cursor:
        cursor.execute("SELECT * FROM pesanan")
        result = cursor.fetchall()
    return jsonify(result)

@app.route('/api/saldo')
def get_saldo():
    with conn.cursor() as cursor:
        cursor.execute("SELECT SUM(total) FROM pemasukan")
        result = cursor.fetchone()
    return jsonify({'saldo': result[0]})

# Jalankan
if __name__ == '__main__':
    app.run(debug=True)
