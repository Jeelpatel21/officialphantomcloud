<?php
$config_file = __DIR__ . '/sheet_config.json';
$google_sheet_url = "https://script.google.com/macros/s/AKfycbyyuRfMctsR2uUZtomahosGyB7pdbV1j-8EdNVFG1goCGBT4q7EvijnkNPHcjvT7MLQ/exec";

if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
    if (!empty($config['web_app_url'])) {
        $google_sheet_url = $config['web_app_url'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $service = $_POST['service'] ?? '';
    $budget  = $_POST['budget'] ?? '';
    $message = $_POST['message'] ?? '';

    // Send to Google Sheets Web App
    $sheet_success = false;
    if (!empty($google_sheet_url)) {
        $post_fields = http_build_query([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'service' => $service,
            'budget'  => $budget,
            'message' => $message
        ]);

        $ch = curl_init($google_sheet_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $sheet_res = curl_exec($ch);
        if ($sheet_res !== false) {
            $sheet_success = true;
        }
        curl_close($ch);
    }

    // Optional MySQL save if configured
    @include_once 'db.php';
    if (isset($conn) && !$conn->connect_error) {
        $sql = "INSERT INTO contacts (name, email, phone, service, budget, message) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssss", $name, $email, $phone, $service, $budget, $message);
            $stmt->execute();
        }
    }

    if ($sheet_success) {
        echo "<div style='font-family:sans-serif;max-width:500px;margin:60px auto;text-align:center;padding:30px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,0.1);background:#f0fdf4;border:1px solid #bbf7d0;'>";
        echo "<h2 style='color:#16a34a;margin-bottom:10px;'>Message Sent Successfully ✅</h2>";
        echo "<p style='color:#374151;'>Your information has been recorded. We will get back to you soon.</p>";
        echo "<a href='contact.html' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#16a34a;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;'>Back to Website</a>";
        echo "</div>";
    } else {
        echo "<div style='font-family:sans-serif;max-width:500px;margin:60px auto;text-align:center;padding:30px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,0.1);background:#fef2f2;border:1px solid #fecaca;'>";
        echo "<h2 style='color:#dc2626;margin-bottom:10px;'>Submission Error ❌</h2>";
        echo "<p style='color:#374151;'>Could not reach the database. Please try again or reach out directly.</p>";
        echo "<a href='contact.html' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#dc2626;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;'>Back to Website</a>";
        echo "</div>";
    }
}
?>