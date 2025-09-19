import subprocess
import os
import joblib
from train import load_data, build_content_model


def test_train_and_artifacts(tmp_path):
    trails_csv = os.path.join('sample_data','trails_sample.csv')
    out_dir = tmp_path / 'artifacts'
    df = load_data(trails_csv)
    build_content_model(df, str(out_dir))
    assert os.path.exists(str(out_dir / 'tfidf_vectorizer.joblib'))
    assert os.path.exists(str(out_dir / 'numeric_scaler.joblib'))
    assert os.path.exists(str(out_dir / 'trail_embeddings.joblib'))
