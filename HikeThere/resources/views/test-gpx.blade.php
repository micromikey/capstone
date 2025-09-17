<!DOCTYPE html>
<html>
<head>
    <title>GPX Auto-Route Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>GPX Auto-Route Test</h1>
    
    <div style="margin-bottom: 20px;">
        <label>Mountain Name: <input type="text" id="test_mountain" value="Mount Pulag"></label><br><br>
        <label>Trail Name: <input type="text" id="test_trail" value=""></label><br><br>
        <label>Location: <input type="text" id="test_location" value="Benguet"></label><br><br>
    </div>
    
    <button onclick="testGPXLibrary()">Test GPX Library</button>
    <button onclick="testAutoRoute()">Test Auto Route</button>
    <button onclick="testWithForm()">Test Auto Route with Form Data</button>
    
    <div id="results"></div>
    
    <script>
        function log(message) {
            document.getElementById('results').innerHTML += '<p>' + message + '</p>';
            console.log(message);
        }
        
        function testGPXLibrary() {
            log('Testing GPX Library...');
            
            fetch('/api/gpx-library', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                log('GPX Library Response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                log('GPX Library Error: ' + error.message);
            });
        }
        
        function testAutoRoute() {
            log('Testing Auto Route Search...');
            
            fetch('/api/gpx-library/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    mountain_name: 'Mount Pulag',
                    trail_name: '',
                    location: 'Benguet'
                })
            })
            .then(response => response.json())
            .then(data => {
                log('Auto Route Response: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                log('Auto Route Error: ' + error.message);
            });
        }
        
        function testWithForm() {
            const mountain = document.getElementById('test_mountain').value;
            const trail = document.getElementById('test_trail').value;
            const location = document.getElementById('test_location').value;
            
            log('Testing with form data: ' + mountain + ', ' + trail + ', ' + location);
            
            fetch('/api/gpx-library/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    mountain_name: mountain,
                    trail_name: trail,
                    location: location
                })
            })
            .then(response => response.json())
            .then(data => {
                log('Form Test Response: ' + JSON.stringify(data, null, 2));
                if (data.success && data.trails && data.trails.length > 0) {
                    log('Found ' + data.trails.length + ' trails!');
                    data.trails.forEach((trail, index) => {
                        log('Trail ' + (index + 1) + ': ' + trail.name + ' (Score: ' + trail.match_score + ')');
                    });
                }
            })
            .catch(error => {
                log('Form Test Error: ' + error.message);
            });
        }
    </script>
</body>
</html>
