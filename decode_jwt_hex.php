<?php
$payload = 'eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im54ZWdjeG5pdWtpYXblvcmJ0bWJyaHQiLCJyb2xlIjoic2VydmljZV9yb2xlIiwiaWF0IjoxNzcwOTY5Njg2LCJleHAiOjIwODY1NDU2ODZ9';
$decoded = base64_decode($payload);
echo bin2hex($decoded) . "\n";
echo $decoded . "\n";
