<?php
/**
 * Test script for public API endpoints
 * This demonstrates that the public endpoints work correctly without authentication
 */

function testEndpoint($url, $description) {
    echo "\n=== Testing: $description ===\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'success') {
            echo "✅ SUCCESS: API returned valid response\n";
            if (isset($data['data'])) {
                if (is_array($data['data']) && isset($data['data']['data'])) {
                    // Paginated response
                    $count = count($data['data']['data']);
                    echo "   - Found $count records\n";
                    if ($count > 0) {
                        echo "   - Sample record keys: " . implode(', ', array_keys($data['data']['data'][0])) . "\n";
                    }
                } elseif (is_array($data['data'])) {
                    // Single record response
                    echo "   - Single record with keys: " . implode(', ', array_keys($data['data'])) . "\n";
                }
            }
        } else {
            echo "❌ ERROR: API returned invalid response format\n";
        }
    } else {
        echo "❌ ERROR: HTTP $httpCode\n";
        echo "Response: " . substr($response, 0, 200) . "...\n";
    }
    
    echo "\n";
}

$baseUrl = 'http://127.0.0.1:8001/api/public';

// Test all public endpoints
testEndpoint("$baseUrl/lamaran-pekerjaan", "Lamaran Pekerjaan List");
testEndpoint("$baseUrl/lamaran-pekerjaan/1", "Lamaran Pekerjaan Detail");
testEndpoint("$baseUrl/wawancara", "Wawancara List");
testEndpoint("$baseUrl/wawancara/1", "Wawancara Detail");
testEndpoint("$baseUrl/hasil-seleksi", "Hasil Seleksi List");
testEndpoint("$baseUrl/hasil-seleksi/1", "Hasil Seleksi Detail");

echo "\n🎉 Public API testing completed!\n";
echo "All endpoints should work without authentication.\n";
echo "These endpoints can be used by:\n";
echo "- External applications\n";
echo "- Frontend dashboards\n";
echo "- Public interfaces\n";
echo "- Third-party integrations\n\n";
?>
