<!DOCTYPE html>
<html>
<head>
    <title>Test AI Generator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Test AI Generator API</h1>
        
        <button onclick="testAPI()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            Test Generate
        </button>
        
        <div id="loading" class="hidden mt-4 text-blue-600">
            Loading...
        </div>
        
        <div id="result" class="mt-4 p-4 bg-gray-50 rounded hidden">
            <h3 class="font-bold mb-2">Result:</h3>
            <pre id="result-text" class="whitespace-pre-wrap text-sm"></pre>
        </div>
        
        <div id="error" class="mt-4 p-4 bg-red-50 rounded hidden">
            <h3 class="font-bold mb-2 text-red-700">Error:</h3>
            <pre id="error-text" class="whitespace-pre-wrap text-sm text-red-600"></pre>
        </div>
        
        <div id="console" class="mt-4 p-4 bg-gray-900 text-green-400 rounded text-xs font-mono">
            <h3 class="font-bold mb-2">Console Log:</h3>
            <div id="console-log"></div>
        </div>
    </div>

    <script>
        function log(message) {
            const consoleLog = document.getElementById('console-log');
            const time = new Date().toLocaleTimeString();
            consoleLog.innerHTML += `[${time}] ${message}<br>`;
            console.log(message);
        }

        async function testAPI() {
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const error = document.getElementById('error');
            
            loading.classList.remove('hidden');
            result.classList.add('hidden');
            error.classList.add('hidden');
            
            log('Starting API test...');
            
            const testData = {
                category: 'social_media',
                subcategory: 'instagram_caption',
                platform: 'instagram',
                brief: 'Kopi arabica premium dari Aceh dengan cita rasa yang khas',
                tone: 'casual',
                keywords: 'kopi, arabica, premium'
            };
            
            log('Request data: ' + JSON.stringify(testData, null, 2));
            
            try {
                log('Sending POST request to /api/ai/generate...');
                
                const response = await fetch('/api/ai/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(testData)
                });
                
                log('Response status: ' + response.status);
                log('Response headers: ' + JSON.stringify([...response.headers.entries()]));
                
                const data = await response.json();
                log('Response data: ' + JSON.stringify(data, null, 2));
                
                loading.classList.add('hidden');
                
                if (data.success) {
                    log('✓ SUCCESS!');
                    result.classList.remove('hidden');
                    document.getElementById('result-text').textContent = data.result;
                } else {
                    log('✗ API returned error');
                    error.classList.remove('hidden');
                    document.getElementById('error-text').textContent = data.message || 'Unknown error';
                }
            } catch (err) {
                log('✗ Exception: ' + err.message);
                log('Stack: ' + err.stack);
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('error-text').textContent = err.message + '\n\n' + err.stack;
            }
        }
        
        log('Test page loaded. Click button to test API.');
    </script>
</body>
</html>
