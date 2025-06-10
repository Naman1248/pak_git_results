<?php

/**
 * Description of MailGun
 *
 * @author SystemAnalyst
 */

namespace components;

class MailGun {

    public function sendMail($to, $toname, $subject, $html, $text, $tag) {
        define('MAILGUN_URL', 'https://api.mailgun.net/v3/gcuonline.pk');
        define('MAILGUN_KEY', '7a57621ae1e228256de404b5b3b4ad4a-24e2ac64-c8d74a5a');
        $mailfromname = 'GCU LAHORE';
        $mailfrom = 'no-reply@gcuonline.pk';
        $replyto = 'no-reply@gcuonline.pk';
        $array_data = array(
            'from' => $mailfromname . '<' . $mailfrom . '>',
            'to' => $toname . '<' . $to . '>',
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
            'o:tracking' => 'yes',
            'o:tracking-clicks' => 'yes',
            'o:tracking-opens' => 'yes',
            'o:tag' => $tag,
            'h:Reply-To' => $replyto
        );
//        $file = '/var/www/gcuonline.pk/test.csv';
//        $mime = mime_content_type($file);
//        $info = pathinfo($file);
//        $name = $info['basename'];
//        $output = new \CURLFile($file, $mime, $name);
        //$array_data["attachment"] = $output;
//        $data = array(
//            "file" => $output,
//            "data" => '{"title":"Test"}'
//        );
        
        $session = curl_init(MAILGUN_URL . '/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:' . MAILGUN_KEY);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);

        return $results;
    }

    public function send() {
        $curl = curl_init();
        $header[] = 'Authorization: Basic ' + base64_encode("api:7a57621ae1e228256de404b5b3b4ad4a-24e2ac64-c8d74a5a");
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mailgun.net/v3/gcuonline.pk/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('from' => 'GCU <no-reply@gcuonline.pk>', 'to' => 'ambernosheen@gmail.com', 'subect' => 'Testing mail gun email service', 'text' => 'Congratulations AMBER NOSHEEN, you just sent an email with Mailgun!  You are truly awesome!"})

# You can see a record of this email in your logs: https://app.mailgun.com/app/logs.

# You can send up to 300 emails/day from this sandbox server.
# Next, you should add your own domain so you can send 10000 emails/month for free.'),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        die('after email');
    }

    /*
      public function curl() {
      exec("curl -s --user 'api:7a57621ae1e228256de404b5b3b4ad4a-24e2ac64-c8d74a5a' \
      https://api.mailgun.net/v3/sandbox6a9efb7bdb794bdd93fb0caf2a6dc91f.mailgun.org/messages \
      -F from='Mailgun Sandbox <postmaster@sandbox6a9efb7bdb794bdd93fb0caf2a6dc91f.mailgun.org>' \
      -F to='AMBER NOSHEEN <ambernosheen@gmail.com>' \
      -F subject='Hello AMBER NOSHEEN' \
      -F text='Congratulations AMBER NOSHEEN, you just sent an email with Mailgun!  You are truly awesome!'

      # You can see a record of this email in your logs: https://app.mailgun.com/app/logs.

      # You can send up to 300 emails/day from this sandbox server.
      # Next, you should add your own domain so you can send 10000 emails/month for free.";
      }
     */
}
