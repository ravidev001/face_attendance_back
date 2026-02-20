import cv2
import sys
import json
import os
from skimage.metrics import structural_similarity as ssim

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CASCADE_PATH = os.path.join(BASE_DIR, "models", "haarcascade_frontalface_default.xml")

face_cascade = cv2.CascadeClassifier(CASCADE_PATH)

def extract_face(image_path):
    img = cv2.imread(image_path)
    if img is None:
        return None

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    faces = face_cascade.detectMultiScale(gray, 1.3, 5)

    if len(faces) == 0:
        return None

    x, y, w, h = faces[0]  # take first detected face
    face = gray[y:y+h, x:x+w]
    face = cv2.resize(face, (200, 200))
    return face

face1 = extract_face(sys.argv[1])
face2 = extract_face(sys.argv[2])

if face1 is None or face2 is None:
    print(json.dumps({
        "status": "NO_FACE_DETECTED",
        "match_percentage": 0
    }))
    exit()

score, _ = ssim(face1, face2, full=True)
match_percentage = round(score * 100, 2)

status = "MATCH" if match_percentage >= 70 else "NO_MATCH"

print(json.dumps({
    "match_percentage": match_percentage,
    "status": status
}))
