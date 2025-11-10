 <?php if($this->getController()):?>
<script src="<?= asset('assets/js/controller/'.$this->getController().'.js'); ?>"></script>
<?php endif; ?>
 <!-- laod dynamic js -->
 <?= $this->stack('js') ?>
