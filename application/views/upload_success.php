<h3>Your file was successfully uploaded!</h3>

<ul>
<?php foreach($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>
<pre>
<?=$s3_upload_data?>
</pre>

<p><?php echo anchor('upload_file', 'Upload another File!'); ?></p>
