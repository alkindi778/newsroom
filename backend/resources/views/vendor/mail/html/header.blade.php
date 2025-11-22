@props(['url'])
@php
$logo = \App\Models\SiteSetting::where('key', 'site_logo')->value('value');
$logoUrl = $logo ? asset('storage/' . $logo) : null;
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if($logoUrl)
<img src="{{ $logoUrl }}" class="logo" alt="{{ config('app.name') }}" style="max-height: 75px; width: auto;">
@else
<span style="font-size: 24px; font-weight: bold; color: #1e2a4a;">{{ config('app.name') }}</span>
@endif
</a>
</td>
</tr>
