"""Train a simple content-based recommender for trails.

Input: CSVs for trails and reviews. Output: artifacts containing vectorizer, scaler, and trail embeddings.
"""
import argparse
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.preprocessing import StandardScaler
from sklearn.metrics.pairwise import cosine_similarity
import joblib
import os

DIFFICULTY_MAP = {'beginner': 0.0, 'intermediate': 1.0, 'advanced': 2.0}


def load_data(trails_csv):
    df = pd.read_csv(trails_csv)
    # Ensure expected columns
    for c in ['id','name','trail_name','description','features','difficulty','length','elevation_gain','latitude','longitude']:
        if c not in df.columns:
            df[c] = None
    # Fill NaNs
    df['features'] = df['features'].fillna('')
    df['description'] = df['description'].fillna('')
    df['combined_text'] = (df['name'].fillna('') + ' ' + df['trail_name'].fillna('') + ' ' + df['description'] + ' ' + df['features'].astype(str))
    df['difficulty_num'] = df['difficulty'].map(DIFFICULTY_MAP).fillna(0.0)
    df['length'] = pd.to_numeric(df['length'], errors='coerce').fillna(0.0)
    df['elevation_gain'] = pd.to_numeric(df['elevation_gain'], errors='coerce').fillna(0.0)
    return df


def build_content_model(df, out_dir):
    os.makedirs(out_dir, exist_ok=True)
    # TF-IDF on combined text
    tfidf = TfidfVectorizer(max_features=2000, ngram_range=(1,2))
    X_text = tfidf.fit_transform(df['combined_text'].values)
    joblib.dump(tfidf, os.path.join(out_dir, 'tfidf_vectorizer.joblib'))

    # Numeric features
    numeric = df[['difficulty_num','length','elevation_gain']].astype(float)
    scaler = StandardScaler()
    X_num = scaler.fit_transform(numeric)
    joblib.dump(scaler, os.path.join(out_dir, 'numeric_scaler.joblib'))

    # Combine into single dense embedding (concatenate)
    X_text_dense = X_text.toarray()
    X = np.hstack([X_text_dense, X_num])
    joblib.dump({'trail_ids': df['id'].tolist(), 'embeddings': X}, os.path.join(out_dir, 'trail_embeddings.joblib'))
    print(f"Saved artifacts to {out_dir}")


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--trails', required=True)
    parser.add_argument('--reviews', required=False)
    parser.add_argument('--out', default='artifacts')
    args = parser.parse_args()

    df_trails = load_data(args.trails)
    build_content_model(df_trails, args.out)
