from flask import Flask, request, jsonify
import insightface
import numpy as np
import base64
import cv2

app = Flask(__name__)

# Load model once
model = insightface.app.FaceAnalysis()
model.prepare(ctx_id=0)

def decode_image(base64_string):
    img_data = base64.b64decode(base64_string)
    np_arr = np.frombuffer(img_data, np.uint8)
    img = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
    return img

def get_embedding(image):
    faces = model.get(image)
    if len(faces) == 0:
        return None
    return faces[0].embedding.tolist()

@app.route("/generate-embedding", methods=["POST"])
def generate_embedding():
    data = request.json

    if not data or "image" not in data:
        return jsonify({"status": False, "message": "No image provided"})

    img = decode_image(data["image"])
    embedding = get_embedding(img)

    if embedding is None:
        return jsonify({"status": False, "message": "No face detected"})

    return jsonify({
        "status": True,
        "embedding": embedding
    })

@app.route("/compare", methods=["POST"])
def compare():
    data = request.json

    emb1 = np.array(data["emb1"])
    emb2 = np.array(data["emb2"])

    similarity = np.dot(emb1, emb2) / (
        np.linalg.norm(emb1) * np.linalg.norm(emb2)
    )

    return jsonify({
        "status": True,
        "similarity": float(similarity)
    })

if __name__ == "__main__":
    app.run(port=5000)
