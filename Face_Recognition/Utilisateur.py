import tkinter as tk
from tkinter import messagebox
import cv2
import os
from PIL import Image
import numpy as np
import mysql.connector
import time
import json

# Répertoire absolu du fichier actuel
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.join(BASE_DIR, "data")

# Réinitialise le fichier JSON au démarrage
with open(os.path.join(BASE_DIR, "recognized_user.json"), "w") as f:
    json.dump({"status": "pending"}, f)

# ========== CONFIGURATION DE LA FENÊTRE ==========
window = tk.Tk()
window.title("Système de Reconnaissance Faciale")
window.configure(bg="#eef2f5")
window.geometry("960x420")
window.resizable(False, False)

# ========== STYLES ==========
LABEL_FONT = ("Segoe UI", 13, "bold")
ENTRY_FONT = ("Segoe UI", 12)
BUTTON_FONT = ("Segoe UI", 12, "bold")
COLOR_PRIMARY = "#3f51b5"
COLOR_SECONDARY = "#f48fb1"
COLOR_SUCCESS = "#4caf50"
COLOR_ACCENT = "#ff9800"
ENTRY_WIDTH = 35
BTN_WIDTH = 20
PAD_X, PAD_Y = 15, 10

# ========== Titre principal ==========
tk.Label(window, text="Interface de reconnaissance faciale!",
         font=("Segoe UI", 20, "bold"), bg="#eef2f5", fg="#3f3f3f").pack(pady=10)

# ========== FRAME BOUTONS ==========
btn_frame = tk.Frame(window, bg="#eef2f5")
btn_frame.pack(pady=20)

def styled_button(parent, text, color, command, column):
    return tk.Button(
        parent,
        text=text,
        font=BUTTON_FONT,
        bg=color,
        fg="white",
        activebackground="#303f9f",
        activeforeground="white",
        relief="flat",
        width=BTN_WIDTH,
        cursor="hand2",
        command=command
    ).grid(row=0, column=column, padx=10)

styled_button(btn_frame, "Cliquer ici pour detecter", COLOR_SUCCESS, lambda: detect_face(), 1)

# ========== Fonctions ==========
def train_classifier():
    path = [os.path.join(DATA_DIR, f) for f in os.listdir(DATA_DIR) if f.endswith(".jpg")]
    faces, ids = [], []

    for image in path:
        img = Image.open(image).convert('L')
        imageNp = np.array(img, 'uint8')
        id = int(os.path.split(image)[1].split(".")[1])
        faces.append(imageNp)
        ids.append(id)

    clf = cv2.face.LBPHFaceRecognizer_create()
    clf.train(faces, np.array(ids))
    clf.write(os.path.join(BASE_DIR, "classifier.xml"))
    messagebox.showinfo('Résultat', 'Entraînement terminé avec succès.')

def detect_face():
    def draw_boundary(img, classifier, scaleFactor, minNeighbors, color, text, clf):
        gray_image = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        features = classifier.detectMultiScale(gray_image, scaleFactor, minNeighbors)

        for (x, y, w, h) in features:
            cv2.rectangle(img, (x, y), (x + w, y + h), color, 2)
            id, pred = clf.predict(gray_image[y:y + h, x:x + w])
            confidence = int(100 * (1 - pred / 300))

            mydb = mysql.connector.connect(
                host="localhost",
                user="root",
                passwd="",
                database="Authorized_user"
            )
            mycursor = mydb.cursor()
            mycursor.execute("SELECT name, address FROM my_table WHERE id=" + str(id))
            s = mycursor.fetchone()

            if confidence > 75 and s:
                name, address = s
                data = {
                    "id": id,
                    "name": name,
                    "address": address,
                    "confidence": confidence,
                    "status": "success"
                }
                with open(os.path.join(BASE_DIR, "recognized_user.json"), "w") as f:
                    json.dump(data, f, indent=4)

                # === Animation : rectangle clignotant + message ===
                for i in range(6):  # 6 fois => ~1 seconde
                    color = (0, 255, 0) if i % 2 == 0 else (0, 150, 0)
                    temp = img.copy()
                    cv2.rectangle(temp, (x, y), (x + w, y + h), color, 3)
                    cv2.putText(temp, f"Bienvenue {name} !", (x, y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.8, color, 2)
                    cv2.imshow("Reconnaissance faciale (10s max)", temp)
                    cv2.waitKey(100)

                return True
            else:
                cv2.putText(img, "UNKNOWN", (x, y - 5), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 0, 255), 1, cv2.LINE_AA)
        return False

    def recognize(img, clf, faceCascade):
        if img is None:
            return img, False
        success = draw_boundary(img, faceCascade, 1.1, 10, (255, 255, 255), "Face", clf)
        return img, success

    faceCascade = cv2.CascadeClassifier(os.path.join(BASE_DIR, "haarcascade_frontalface_default.xml"))
    clf = cv2.face.LBPHFaceRecognizer_create()
    clf.read(os.path.join(BASE_DIR, "classifier.xml"))

    video_capture = cv2.VideoCapture(0, cv2.CAP_DSHOW)
    start_time = time.time()
    recognized = False

    while True:
        ret, img = video_capture.read()
        if not ret or img is None:
            continue

        img, recognized = recognize(img, clf, faceCascade)
        cv2.imshow("Reconnaissance faciale (10s max)", img)

        if recognized or (time.time() - start_time > 10):
            break

        if cv2.waitKey(1) == 13:
            break

    video_capture.release()
    cv2.destroyAllWindows()

    if recognized:
        window.destroy()
    else:
        with open(os.path.join(BASE_DIR, "recognized_user.json"), "w") as f:
            json.dump({"status": "fail"}, f)

# ========== Démarrage de la boucle ==========
window.mainloop()
