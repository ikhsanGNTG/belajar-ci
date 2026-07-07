<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

<?= form_hidden('username', session()->get('username')) ?>
<?= form_hidden('total_harga', '') ?>
<?= form_hidden('kupon_code', '') ?>
<?= form_hidden('diskon_kupon', '0') ?>
<?= form_hidden('biaya_admin', '0') ?>
<?= form_hidden('cashback', '0') ?>
<?= form_hidden('grand_total', '') ?>

<div class="col-12">
    <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'nama',
        'id'       => 'nama',
        'class'    => 'form-control',
        'value'    => session()->get('username'),
        'readonly' => true]) ?>
</div>
<div class="col-12">
    <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'  => 'alamat',
        'id'    => 'alamat',
        'class' => 'form-control']) ?>
</div> 
<div class="col-12"> 
    <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
    <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
</div>
<div class="col-12"> 
    <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
    <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
</div>
<div class="col-12">
    <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'ongkir',
        'id'       => 'ongkir',
        'class'    => 'form-control',
        'readonly' => true]) ?>
</div>

<div class="col-12">
<?= form_label('Kupon Promo', 'kupon_code_input', ['class' => 'form-label']) ?>
    <input type="text" name="kupon_code_input" id="kupon_code_input" class="form-control" placeholder="Tersedia: HEMAT (15%), SUPER (20%)" />
    <input type="hidden" name="kupon_code" id="kupon_code" value="" />
</div>
<div class="col-12">
    <?= form_submit(
        'submit',
        'Buat Pesanan',
        ['class' => 'btn btn-primary']) ?>
</div>

<?= form_close() ?> 
    </div>
    <div class="col-lg-6">

        <table class="table">
  <thead>
      <tr>
          <th scope="col">Nama</th>
          <th scope="col">Harga</th>
          <th scope="col">Jumlah</th>
          <th scope="col">Sub Total</th>
      </tr>
  </thead>
  <tbody>
      <?php 
      if (!empty($items)) :
          foreach ($items as $index => $item) :
      ?>
              <tr>
                  <td><?= $item['name'] ?></td>
                  <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                  <td><?= $item['qty'] ?></td>
                  <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
              </tr>
      <?php
          endforeach;
      endif;
      ?>
      <tr>
          <td colspan="2"></td>
          <td>Subtotal</td>
          <td id="subtotal_show"><?= number_to_currency($total, 'IDR') ?></td>
      </tr>
      <tr>
          <td colspan="2"></td>
          <td>Diskon Kupon</td>
          <td><span id="diskon_kupon_show">Rp 0</span></td>
      </tr>
      <tr>
          <td colspan="2"></td>
          <td>Biaya Admin</td>
          <td><span id="biaya_admin_show">Rp 0</span></td>
      </tr>
      <tr>
          <td colspan="2"></td>
          <td>Cashback</td>
          <td><span id="cashback_show">Rp 0</span></td>
      </tr>
      <tr>
          <td colspan="2"></td>
          <td>Subtotal ( +Admin-Kupon )</td>
          <td><span id="subtotal_admin_kupon_show"><?= number_to_currency($total, 'IDR') ?></span></td>
      </tr>
      <tr>
          <td colspan="2"></td>
          <td>Grand Total (incl. Ongkir)</td>
          <td><span id="total"><?= number_to_currency($total, 'IDR') ?></span></td>
      </tr>
  </tbody>
</table>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
let ongkir = 0;
let subtotal = <?= $total ?>;
hitungTotal();

// update perhitungan saat kupon diketik
$('#kupon_code_input').on('input', function() {
    hitungTotal();
});


function formatIDR(num) {
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

function hitungAdminKuponCashback() {
    const kupon = ($('#kupon_code_input').val() || '').trim().toUpperCase();

    // helper logic must match server
    const biayaAdmin = (subtotal <= 20000000) ? (subtotal * 0.005) : (subtotal * 0.0075);

    let diskonRate = 0;
    if (kupon === 'HEMAT') diskonRate = 0.15;
    if (kupon === 'SUPER') diskonRate = 0.20;

    const diskonKupon = subtotal * diskonRate;
    const cashback = (subtotal > 10000000) ? (subtotal * 0.02) : 0;

    // update hidden inputs for server
    $('#kupon_code').val(kupon || '');
    $('#diskon_kupon').val(diskonKupon);
    $('#biaya_admin').val(biayaAdmin);
    $('#cashback').val(cashback);

    // subtotal (+admin - kupon)
    const subtotalAdminKupon = subtotal - diskonKupon + biayaAdmin;
    $('#subtotal_admin_kupon_show').text(formatIDR(subtotalAdminKupon));

    // grand total includes ongkir and cashback per spec
    const grandTotal = subtotalAdminKupon + cashback + ongkir;
    $('#grand_total').val(grandTotal);

    $('#diskon_kupon_show').text(formatIDR(diskonKupon));
    $('#biaya_admin_show').text(formatIDR(biayaAdmin));
    $('#cashback_show').text(formatIDR(cashback));

    return grandTotal;
}

function hitungTotal() {
    let grandTotal = hitungAdminKuponCashback();

    $("#ongkir").val(ongkir);
    $("#total").text(`IDR ${Math.round(grandTotal).toLocaleString('id-ID')}`);
    $("#total_harga").val(grandTotal);
}

	$('#kelurahan').select2({
	    placeholder: 'Cari daerah tujuan',
	    minimumInputLength: 3,
        ajax: {
    url: '<?= site_url('ajax/destinations') ?>',
    dataType: 'json',
    delay: 300,
    data: function(params) {
        return {
            q: params.term
        };
    },
    processResults: function(data) {
        return data;
    },
    cache: true
    } 
	});

    $("#kelurahan").on('change', function () {
    let id_kelurahan = $(this).val();

    $("#layanan").empty();
    ongkir = 0;
    hitungTotal();
    
 $.ajax({
    url: "<?= site_url('ajax/costs') ?>", 
    dataType: "json",
    data: {
        destination: id_kelurahan
    },
    success: function (data) { 
        data.forEach(function (item) {
            $("#layanan").append(
                $('<option>', {
                    value: item.cost,
                    text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                })
            );
        });
    }
});
});

$("#layanan").on('change', function() {
    ongkir = parseInt($(this).val());
    hitungTotal();
}); 

});
</script>
<?= $this->endSection() ?>