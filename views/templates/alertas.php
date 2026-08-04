<?php if(isset($alertas) && !empty($alertas)): ?>
    <?php foreach ($alertas as $tipo => $mensajes): ?>
        <?php foreach ($mensajes as $mensaje): ?>
            <div class="alerta alerta__<?php echo $tipo; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>