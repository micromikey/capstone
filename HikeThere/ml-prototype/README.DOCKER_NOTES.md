Extra notes about Docker and Windows

- On Windows, when using PowerShell, ensure Docker Desktop is installed and running.
- If using Docker Toolbox or a VM, adjust host networking accordingly.
- If you want to run training inside the container:

  docker run --rm -v ${PWD}/artifacts:/app/artifacts -e ML_ARTIFACT_DIR=/app/artifacts hike-ml:latest python train.py --trails sample_data/trails_sample.csv --out artifacts

- The image installs build-essential to make package installs more robust. If you want a smaller image and you have a conda environment locally, prefer Conda/local instead of Docker.
