@php
    $hospital = \App\Helpers\ReportHelper::getHospitalProfile();
@endphp

{{-- Standard Kop Surat Rumah Sakit --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
    <tr>
        @if (!empty($hospital['logo']))
            <td style="width: 65px; vertical-align: middle; text-align: left; padding-right: 10px; padding-bottom: 4px;">
                <img src="{{ $hospital['logo'] }}" alt="Logo" style="height: 55px; width: auto; max-width: 65px; object-fit: contain;">
            </td>
        @endif
        <td style="vertical-align: middle; text-align: left; padding-bottom: 4px;">
            <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; color: #0f172a; line-height: 1.2;">
                {{ $hospital['nama'] }}
            </div>
            <div style="font-size: 9.5px; color: #334155; margin-top: 2px; line-height: 1.3;">
                {{ $hospital['alamat'] }}
            </div>
            <div style="font-size: 9px; color: #475569; margin-top: 1px; line-height: 1.3;">
                <span>Telp: {{ $hospital['telepon'] }}</span>
                <span style="margin: 0 6px;">|</span>
                <span>Email: {{ $hospital['email'] }}</span>
            </div>
        </td>
    </tr>
</table>

{{-- Garis 2 Kop Resmi (Garis tebal atas + Garis tipis bawah) --}}
<div style="border-top: 2px solid #0f172a; margin-top: 3px;"></div>
<div style="border-top: 1px solid #0f172a; margin-top: 1.5px; margin-bottom: 12px;"></div>
