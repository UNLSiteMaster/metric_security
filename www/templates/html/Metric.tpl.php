<p>
    This security metric checks for several security and privacy related problems on your site. At the moment it focuses on https usage and checks for insecure assets known as <a href="https://developer.mozilla.org/en-US/docs/Web/Security/Mixed_content">mixed content</a>. This metric will not perform any security checks that may potentially harm your site.
</p>

<?php
$included_file = __DIR__.'/../../../custom-message.html';
if (file_exists($included_file)) {
    include $included_file;
}

?>

