import cv2
import mysql.connector
import time
import sys

# Chargement des classifieurs
faceCascade = cv2.CascadeClassifier("haarcascade_frontalface_default.xml")
clf = cv2.face.LBPHFaceRecognizer_create()
clf.read("classifier.xml")

# Ouverture webcam
video_capture = cv2.VideoCapture(0, cv2.CAP_DSHOW)

if not video_capture.isOpened():
    print("Erreur : webcam non disponible.")
    sys.exit()

timeout = time.time() + 15  # 15 secondes max
recognized = False

while True:
    if time.time() > timeout:
        break

    ret, img = video_capture.read()
    if not ret or img is None:
        continue

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    faces = faceCascade.detectMultiScale(gray, 1.3, 5)

    for (x, y, w, h) in faces:
        id, pred = clf.predict(gray[y:y+h, x:x+w])
        confidence = int(100 * (1 - pred / 300))

        if confidence >= 74:
            try:
                mydb = mysql.connector.connect(
                    host="localhost",
                    user="root",
                    passwd="",
                    database="Authorized_user"
                )
                mycursor = mydb.cursor()
                mycursor.execute("SELECT name FROM my_table WHERE id = %s", (id,))
                result = mycursor.fetchone()

                if result and confidence >= 74:
                    print("OK")
                    recognized = True
                    break

            except Exception as e:
                print("Erreur DB:", e)
                break

    if recognized:
        break

    # Pas de cv2.imshow ici pour usage serveur

video_capture.release()
cv2.destroyAllWindows()

if not recognized:
    sys.exit(1)  # Rien n'est imprimé si échec
