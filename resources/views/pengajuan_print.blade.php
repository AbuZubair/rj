<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengajuan</title>

    <style>

        h4 {
            margin: 0;
        }
        .w-full {
            width: 100%;
        }
        .w-half {
            width: 50%;
        }
        .margin-top {
            margin-top: 1.25rem;
        }
        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        table.products {
            font-size: 0.875rem;
        }
        table.products tr {
            background-color: rgb(96 165 250);
        }
        table.products th {
            color: #ffffff;
            padding: 0.5rem;
        }
        table tr td.header{
            width: 30%;
        }
        table tr td.separator {
            width: 10px;
        }
        table tr.items {
            background-color: rgb(241 245 249);
        }
        table tr.items td {
            padding: 0.5rem;
            text-align: center;
        }
        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }

	</style>
</head>
<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <h2>Pengajuan Kredit</h2>
            </td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="header">
                    <h4>Nama</h4>
                </td>
                <td class="separator">:</td>
                <td>
                    <span> {{ $data->fullname }}</span>
                </td>
            </tr>
            <tr>
                <td class="header">
                    <h4>No Anggota</h4>
                </td>
                <td class="separator">:</td>
                <td>
                    <span> {{ $data->no_anggota }}</span>
                </td>
            </tr>
            <tr>
                <td class="header">
                    <h4>Tanggal Pengajuan</h4>
                </td>
                <td class="separator">:</td>
                <td>
                    <span> {{ $data->date }}</span>
                </td>
            </tr>
        </table>
    </div>
 
    <div class="margin-top">
        <table class="products">
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Harga Item</th>
                <th>Harga Total</th>
                <th>Angsuran</th>
                <th>Margin</th>
            </tr>
 
            <tr class="items">
                <td>
                    {{ $data->type === 0 ? 'Barang' : 'Jasa' }}
                </td>
                <td>
                    {{ $data->desc }}
                </td>
                <td>
                    @money($data->nilai_awal)
                </td>
                <td>
                    @money($data->nilai_total)
                </td>
                <td>
                    @money($data->angsuran)
                </td>
                <td>
                    {{ $data->margin }}
                </td>
            </tr>
        </table>
    </div>
 
    <div class="footer margin-top">
        <div>Ttd</div>
        <div>{{ $data->fullname }}</div>
    </div>
</body>
</html>