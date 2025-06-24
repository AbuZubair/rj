
<p>Hello, </p>

<h5>Pengajuan baru diterima, dengan rincian sebagai berikut: </h5>
<p>No Anggota : {{$data['no_anggota']}}</p>
<p>Nama : {{$data['fullname']}}</p>
<p>Detail: </p>
<table style="border-style:solid; border-width:1px; border-color:#000000;"> 
    <thead>
        <tr>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Date</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Type</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Deskripsi</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Harga Item</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Harga Total</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Angsuran</th>
            <th style="border-style:solid; border-width:1px; border-color:#000000;text-align:left; padding: 5px">Margin</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{$data['date']}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{$data['type'] == 0 ? 'Barang' : 'Jasa'}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{$data['desc']}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{number_format($data['nilai_awal'])}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{number_format($data['nilai_total'])}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{number_format($data['angsuran'])}}</td>
            <td style="border-style:solid; border-width:1px; border-color:#000000;padding: 5px">{{$data['margin']}}</td>
        </tr>
    </tbody>
</table><br>
 
<p>Best Regards,</p>