@props(['status' => 'info', 'width' => 'w-1/2'])

@php
if($status === 'info'){$bgColor = 'bg-blue-300';}
if($status === 'success'){$bgColor = 'bg-green-300';}
if($status === 'error'){$bgColor = 'bg-red-300';}
if($status === 'blockedUser'){$bgColor = 'bg-orange-300';}
if($status === 'suspension'){$bgColor = 'bg-yellow-400';}
if($status === 'not_member'){$bgColor = 'bg-gray-400';}
if($status === 'isNotJoinFreeEnabled'){$bgColor = 'bg-purple-400';}
if($status === 'role-access-error'){$bgColor = 'bg-pink-400';}
@endphp

@if (session($status))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="{{ $bgColor }} {{ $width }} mx-auto m-5 p-2 text-center rounded-2xl font-bold text-white">
  {!! nl2br(e(session($status))) !!}
</div>
@endif