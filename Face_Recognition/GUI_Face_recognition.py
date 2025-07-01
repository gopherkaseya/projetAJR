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
tk.Label(window, text="Formulaire d'Enregistrement Utilisateur",
         font=("Segoe UI", 20, "bold"), bg="#eef2f5", fg="#3f3f3f").pack(pady=10)

# ========== FRAME FORMULAIRE ==========
form_frame = tk.Frame(window, bg="#eef2f5")
form_frame.pack(pady=10)

tk.Label(form_frame, text="Nom :", font=LABEL_FONT, bg="#eef2f5").grid(row=0, column=0, sticky='e', padx=PAD_X, pady=PAD_Y)
t1 = tk.Entry(form_frame, font=ENTRY_FONT, width=ENTRY_WIDTH)
t1.grid(row=0, column=1, padx=PAD_X, pady=PAD_Y)

tk.Label(form_frame, text="Âge :", font=LABEL_FONT, bg="#eef2f5").grid(row=1, column=0, sticky='e', padx=PAD_X, pady=PAD_Y)
t2 = tk.Entry(form_frame, font=ENTRY_FONT, width=ENTRY_WIDTH)
t2.grid(row=1, column=1, padx=PAD_X, pady=PAD_Y)

tk.Label(form_frame, text="Adresse :", font=LABEL_FONT, bg="#eef2f5").grid(row=2, column=0, sticky='e', padx=PAD_X, pady=PAD_Y)
t3 = tk.Entry(form_frame, font=ENTRY_FONT, width=ENTRY_WIDTH)
t3.grid(row=2, column=1, padx=PAD_X, pady=PAD_Y)

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

styled_button(btn_frame, "Entraîner", COLOR_ACCENT, lambda: train_classifier(), 0)
styled_button(btn_frame, "Détecter visage", COLOR_SUCCESS, lambda: detect_face(), 1)
styled_button(btn_frame, "Générer dataset", COLOR_SECONDARY, lambda: generate_dataset(), 2)

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
                    "status" : "success"
                }
                with open(os.path.join(BASE_DIR, "recognized_user.json"), "w") as f:
                    json.dump(data, f, indent=4)
                cv2.putText(img, name, (x, y - 5), cv2.FONT_HERSHEY_SIMPLEX, 0.8, color, 1, cv2.LINE_AA)
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

def generate_dataset():
    if t1.get() == "" or t2.get() == "" or t3.get() == "":
        messagebox.showinfo('Erreur', 'Veuillez remplir tous les champs.')
        return

    mydb = mysql.connector.connect(host="localhost", user="root", passwd="", database="Authorized_user")
    mycursor = mydb.cursor()
    mycursor.execute("SELECT * FROM my_table")
    id = len(mycursor.fetchall()) + 1

    sql = "INSERT INTO my_table(id, Name, Age, Address) VALUES (%s, %s, %s, %s)"
    val = (id, t1.get(), t2.get(), t3.get())
    mycursor.execute(sql, val)
    mydb.commit()

    face_classifier = cv2.CascadeClassifier(os.path.join(BASE_DIR, "haarcascade_frontalface_default.xml"))

    def face_cropped(img):
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        faces = face_classifier.detectMultiScale(gray, 1.3, 5)
        if len(faces) == 0:
            return None
        for (x, y, w, h) in faces:
            return img[y:y + h, x:x + w]

    cap = cv2.VideoCapture(0, cv2.CAP_DSHOW)
    img_id = 0

    while True:
        ret, frame = cap.read()
        if not ret or frame is None:
            continue

        face = face_cropped(frame)
        if face is not None:
            img_id += 1
            face = cv2.resize(face, (200, 200))
            face = cv2.cvtColor(face, cv2.COLOR_BGR2GRAY)
            file_name_path = os.path.join(DATA_DIR, f"user.{id}.{img_id}.jpg")
            cv2.imwrite(file_name_path, face)
            cv2.putText(face, str(img_id), (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
            cv2.imshow("Cropped Face", face)

        if cv2.waitKey(1) == 13 or img_id == 100:
            break

    cap.release()
    cv2.destroyAllWindows()
    messagebox.showinfo('Succès', 'Dataset généré avec succès !')

# ========== Démarrage de la boucle ==========
window.mainloop()
    