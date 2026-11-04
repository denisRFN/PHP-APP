<div class="message error ajax-errors" <?php echo empty($errors) ? 'style="display: none;"' : ''; ?>>
    <?php foreach ($errors as $error): ?>
        <p><?php echo showValue($error); ?></p>
    <?php endforeach; ?>
</div>
