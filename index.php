<?php
$error = '';
$revised_prompt = '';
$image = '';

if (isset($_POST['prompt']) && !empty($_POST['prompt'])) {
    $prompt = $_POST['prompt'];
    $api_key = '';
    $url = 'https://api.openai.com/v1/images/generations';
    $data = array(
        "model" => "dall-e-3",
        "prompt" =>  $prompt,
        "n" => 1
    );

    $payload = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $api_key));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    // Get any error if any
    if (curl_errno($ch)) {
        $error = curl_error($ch);
    } else {
        $result = json_decode($result);
        $revised_prompt = $result->data[0]->revised_prompt;
        $image = $result->data[0]->url;
    }

    curl_close($ch);

    // Set the content type header to JSON
    header('Content-Type: application/json');

    // Return a JSON response
    echo json_encode(array('error' => $error, 'revised_prompt' => $revised_prompt, 'image' => $image));
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Image Generation</title>
</head>
<style>
    .sprimary {
        color: #F64A1C !important;
    }

    .sprimary-bg {
        background-color: #F64A1C !important;
    }
</style>
<body>

<div class="container">
    <div class="my-5">
        <h1>Think of an image? Generate it</h1>
        <div id="error-container" class="my-3"></div>
        <div id="result-container" class="my-3"></div>
        <form id="generation-form">
            <div class="form-group">
                <label for="prompt">Explain the image you want to generate</label>
                <input type="text" name="prompt" id="prompt" class="form-control">
            </div>

            <div class="form-group mt-2">
                <button type="button" id="generate-btn" class="btn btn-primary sprimary-bg">Generate</button>
                <div id="loader" class="d-none spinner-border text-primary" role="status">
                    <span class="sr-only">Generating</span>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    function toggleLoadingState(){
        $('#generate-btn').toggleClass('d-none');
        $('#loader').toggleClass('d-none');
    }

    $(document).ready(function () {
        $('#generate-btn').click(function () {
            var prompt = $('#prompt').val();
            if (prompt === '') {
                $('#error-container').html('<div class="alert alert-danger">Prompt is required</div>');
                return;
            }
            toggleLoadingState();
            $.ajax({
                type: 'POST',
                url: window.location.href, 
                data: {prompt: prompt},
                success: function (response) {
                    console.log(response)
                    if (response.error) {
                        $('#error-container').html('<div class="alert alert-danger">' + response.error + '</div>');
                        $('#result-container').html('');
                    } else {
                        $('#error-container').html('');
                        $('#result-container').html('<div class="my-3">' +
                            '<img src="' + response.image + '" alt="Generated Image" class="img-fluid">' +
                            '<div class="my-1">' +
                            '<h4>Revised Prompt</h4>' +
                            '<p>' + response.revised_prompt + '</p>' +
                            '</div>' +
                            '<a href="' + response.image + '" class="btn btn-primary btn-sm sprimary-bg mt-2">Download</a>' +
                            '</div>');
                    }
                    toggleLoadingState();
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        });
    });
</script>
</body>
</html>

