ML Prototype for Trail Recommendations

Quick start (local prototype using sample data)

1) Create a Python virtualenv and install dependencies:

   python -m venv .venv; .\.venv\Scripts\Activate; pip install -r requirements.txt

2) Train the content-based model using sample data:

   python train.py --trails sample_data/trails_sample.csv --reviews sample_data/reviews_sample.csv --out artifacts

3) Run the API server:

   uvicorn serve:app --reload --port 8001

4) Example request (curl):

   curl -X POST "http://localhost:8001/recommend" -H "Content-Type: application/json" -d "{\"user_profile\": {\"preferred_difficulty\": \"beginner\", \"preferred_tags\": [\"waterfall\"], \"location\": {\"lat\": 16.4, \"lon\": 120.6}}, \"k\": 5}"

Notes
- This is a minimal prototype: replace the CSV exports with real exports from your Laravel DB. See `DESIGN.md` for SQL examples to extract `trails` and `reviews`.
- For production use: add authentication, rate-limiting, model versioning, and scheduled retraining.

## Docker (recommended)

Build and run the prototype using Docker. This avoids local build issues on Windows.

Build the image from the `ml-prototype` folder:

```powershell
# From the repo root
cd "ml-prototype"
docker build -t hike-ml:latest .
```

Run the container:

```powershell
# Run locally and map port 8001
docker run --rm -p 8001:8001 -v ${PWD}/artifacts:/app/artifacts -e ML_ARTIFACT_DIR=/app/artifacts hike-ml:latest
```

Or use docker-compose from the `ml-prototype` folder:

```powershell
cd "ml-prototype"
docker-compose up --build
```

Notes:
- The container will expose the FastAPI server on port `8001`.
- Mounting `./artifacts` allows trained artifacts to be persisted outside the container. You can run the `train.py` inside the container or copy artifacts in before starting.
- If you prefer to train outside Docker, generate `artifacts` locally and then run the container with the volume mount to serve the model.

