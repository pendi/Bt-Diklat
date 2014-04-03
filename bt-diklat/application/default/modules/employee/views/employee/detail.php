<?php $title = 'Detail Pegawai' ?>
<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>

<div class="clearfix"></div>
<fieldset>
    <legend><?php echo l('Data Utama Pegawai') ?></legend>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Nama Pegawai') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['employee_name'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('NIP') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['nip'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Golongan') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['group_name'] ?></div>
            </div>
        </div>
        <div class="span6">
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Pangkat') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['rank'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Jabatan') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['position'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Unit Kerja') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['unit_of_work'] ?></div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo l('Riwayat Pedidikan Terakhir') ?></legend>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Pendidikan Terakhir') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['level_name'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Nama Sekolah') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['name_of_school'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Jurusan') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['majors'] ?></div>
            </div>
        </div>
        <div class="span6">
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Tahun Lulus') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['year_pass'] ?></div>
            </div>
            <div class="row-fluid">
                <div class="span3"><b><?php echo l('Keterangan Kelulusan') ?></b></div>
                <div class="span1"><b><?php echo l(':') ?></b></div>
                <div class="span8"><?php echo $data['desc_of_education'] ?></div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo l('Diklat Yang Pernah Diikuti') ?></legend>
    <div class="row-fluid">
        <table class="table table-bordered table-striped">
            <tr class="success">
                <th><?php echo l('Nama Diklat') ?></th>
                <th><?php echo l('Tempat') ?></th>
                <th><?php echo l('Tahun') ?></th>
                <th><?php echo l('Keterangan') ?></th>
            </tr>
            <tbody>
                <?php if($diklat): ?>
                    <?php foreach($diklat as $key => $val): ?>
                        <tr>
                            <td><?php echo $val['course_name'] ?></td>
                            <td><?php echo $val['place'] ?></td>
                            <td><?php echo $val['year'] ?></td>
                            <td><?php echo $val['description'] ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td style="text-align: center" colspan="4">Data tidak tersedia</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</fieldset>



