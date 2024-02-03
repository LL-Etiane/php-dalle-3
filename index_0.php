<?php
    $error = '';
    $image = '';
    if(isset($_POST['prompt']) && !empty($_POST['prompt'])){
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
            return $error;
        }else{
            $result = json_decode($result);
            $revised_prompt = $result->data[0]->revised_prompt;
            $image = $result->data[0]->url;
        }
        curl_close($ch);
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
  <body>
   
    <div class="container">
        <div class="my-5">
            <h1>Think of an image? Generate it</h1>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="my-3">
                <?php if(!empty($image)): ?>
                    <img src="<?php echo $image; ?>" alt="Generated Image" class="img-fluid">
                    <div class="my-1">
                        <h4>Revised Prompt</h4>
                        <p><?php echo $revised_prompt; ?></p>
                    </div>
                    <a href="<?php echo $image ?>" class="btn btn-primary btn-sm mt-2">Download</a>
                <?php endif; ?>
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="prompt">Explain the image you want to generate</label>
                    <input type="text" name="prompt" id="prompt" class="form-control">
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  </body>
</html>