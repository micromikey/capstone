# Minimal Dockerfile for the ML prototype
FROM python:3.10-slim

ENV PYTHONUNBUFFERED=1
WORKDIR /app

# Install build deps that may be needed on some packages (numpy/pandas)
RUN apt-get update \
    && apt-get install -y --no-install-recommends build-essential gcc g++ libpq-dev curl ca-certificates \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt /app/requirements.txt
RUN python -m pip install --upgrade pip setuptools wheel \
    && pip install --no-cache-dir -r /app/requirements.txt

# Copy the rest of the prototype
COPY . /app

# Expose FastAPI port
EXPOSE 8001

# Default command to run the server
CMD ["uvicorn", "serve:app", "--host", "0.0.0.0", "--port", "8001"]
