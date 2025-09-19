"""FastAPI service to serve content-based trail recommendations.

This loads artifacts produced by `train.py` and exposes a /recommend endpoint.
"""
from fastapi import FastAPI
from pydantic import BaseModel
import joblib
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
import os

app = FastAPI()
ARTIFACT_DIR = os.environ.get('ML_ARTIFACT_DIR', 'artifacts')

# Load artifacts lazily
artifacts = {}

def ensure_loaded():
    global artifacts
    if artifacts:
        return
    artifacts['tfidf'] = joblib.load(os.path.join(ARTIFACT_DIR, 'tfidf_vectorizer.joblib'))
    artifacts['scaler'] = joblib.load(os.path.join(ARTIFACT_DIR, 'numeric_scaler.joblib'))
    emb = joblib.load(os.path.join(ARTIFACT_DIR, 'trail_embeddings.joblib'))
    artifacts['trail_ids'] = emb['trail_ids']
    artifacts['embeddings'] = emb['embeddings']

class Location(BaseModel):
    lat: float
    lon: float

class UserProfile(BaseModel):
    preferred_difficulty: str = None
    preferred_tags: list[str] = []
    location: Location | None = None
    liked_trail_ids: list[int] = []

class RecommendRequest(BaseModel):
    user_profile: UserProfile
    k: int = 5

@app.post('/recommend')
def recommend(req: RecommendRequest):
    ensure_loaded()
    tfidf = artifacts['tfidf']
    scaler = artifacts['scaler']
    trail_ids = artifacts['trail_ids']
    embeddings = artifacts['embeddings']

    # Build a pseudo user vector from profile
    texts = []
    if req.user_profile.preferred_tags:
        texts.append(' '.join(req.user_profile.preferred_tags))

    # difficulty
    diff_map = {'beginner':0.0,'intermediate':1.0,'advanced':2.0}
    diff_num = diff_map.get(req.user_profile.preferred_difficulty, 0.0)

    # Text vector
    user_text_vec = tfidf.transform([' '.join(texts)]) if texts else tfidf.transform([''])
    user_text_vec = user_text_vec.toarray()

    # Numeric vector
    user_num = scaler.transform([[diff_num, 0.0, 0.0]])  # length/elevation unknown

    user_vec = np.hstack([user_text_vec, user_num])

    # Compute cosine similarities
    sims = cosine_similarity(user_vec, embeddings)[0]
    top_idx = np.argsort(-sims)[:req.k]
    results = []
    for idx in top_idx:
        results.append({'trail_id': int(trail_ids[idx]), 'score': float(sims[idx])})
    return {'results': results}
