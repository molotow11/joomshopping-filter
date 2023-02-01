<?php defined('_JEXEC') or die(); ?>
<table class="jshop list_product">

	<?php foreach ($this->results as $k=>$product) : ?>

	<?php if ($k % $this->count_product_to_row == 0) echo "<tr>"; ?>
	
		<td width="<?php echo 100 / $this->count_product_to_row ?>%" class="block_product">
			<?php include(dirname(__FILE__)."/product.php");?>
		</td>
		
		<?php if ($k % $this->count_product_to_row == $this->count_product_to_row - 1) : ?>
		</tr>
		<tr>
			<td colspan="<?php echo $this->count_product_to_row; ?>"><div class="product_list_hr"></div></td>
		</tr>                
		<?php endif; ?>

	<?php endforeach; ?>

	<?php if ($k % $this->count_product_to_row != $this->count_product_to_row - 1) echo "</tr>";?>

</table>