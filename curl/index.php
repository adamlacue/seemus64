<pre><?php

$api_key = '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';  // Replace with your OpenAI API key
$api_url = 'https://api.openai.com/v1/chat/completions';  // URL for ChatGPT API

// Data you want to send in the request
$data = [
    'model' => 'gpt-3.5-turbo',  // Specify the model, e.g., gpt-4.0-chat
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => 'Tell me about the klr 650 3rd gen.']
    ]
];


// Setup cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json',
    'OpenAI-Organization: org-Xrqnnso5yvIMTTHHljIr6H1u'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Decode the response
    $decoded_response = json_decode($response, true);
    print_r($decoded_response);
}

// Close the cURL session
curl_close($ch);
?></pre>