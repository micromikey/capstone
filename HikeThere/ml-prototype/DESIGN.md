Trail Recommendation ML Design

Goal
- Provide personalized trail recommendations using available trail metadata and user interactions (reviews, itineraries, assessments, preferences).

Overview
- Hybrid approach: start with a content-based recommender (fast, explainable) and add collaborative filtering (CF) later as interaction data grows. Provide fallback popularity-based and distance-filtered recommendations.

Data sources in this repository (inventory)
- `app/Models/Trail.php` — trail attributes: `difficulty`, `length`, `elevation_gain`, `features` (array), `description`, `coordinates`, `geometry`, `latitude`, `longitude`.
- `app/Models/TrailReview.php` — user interactions: ratings (`rating`), `trail_id`, `user_id`, `hike_date`, conditions and review text.
- `database/migrations` — contains migrations for itineraries and route data; itineraries route data exists and can be exported.

Feature engineering
- Text features: `trail.name`, `trail.trail_name`, `trail.summary`, `trail.description`, `features` (array). Use TF-IDF on concatenated text.
- Categorical/ordinal: `difficulty` mapped to numeric (beginner=0, intermediate=1, advanced=2).
- Numeric: `length`, `elevation_gain`, `estimated_time` (normalize).
- Geographic: `latitude`, `longitude` (for distance filtering or to create spatial features via Haversine or grid).
- Interaction features: user ratings and follows — used for collaborative filtering later.

Model choices
- Phase 1 (prototype): Content-based nearest-neighbor using TF-IDF (text) + normalized numeric features; similarity via cosine.
- Phase 2: Add collaborative filtering via matrix factorization (e.g., `implicit` ALS for implicit signals or `surprise` / `lightfm` for explicit ratings).
- Hybrid: combine CF and content scores (weighted sum) and re-rank by distance / season / readiness.

Serving architecture
- Offline training script (`train.py`) produces serialized artifacts: vectorizer, numeric scaler, trail embeddings (joblib files).
- Lightweight REST service (FastAPI `serve.py`) that loads artifacts and returns `recommend(user_profile, k)`.
- Laravel integration options:
  - Call the FastAPI endpoint from Laravel (HTTP) for inference.
  - Or implement a PHP fallback: simple query-based content matching on `features` using SQL full-text if Python service unavailable.

Privacy & consent
- Only use anonymized interaction data; do not expose raw PII. Ensure users opt-in for using activity history in recommendations.

Evaluation
- Metrics: Precision@K, Recall@K, MAP, and offline holdout evaluation. Start with simple holdout of most recent reviews per user.

Next steps to integrate
1. Export data from DB: provide SQL queries in README to get `trails.csv` and `reviews.csv`.
2. Run `train.py` locally with exported CSVs to build models.
3. Start `serve.py` and call `/recommend` from Laravel.

