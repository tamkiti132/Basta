@props(['status' => 'info'])

@php
if($status === 'info'){$bgColor = 'bg-blue-300';}
if($status === 'error'){$bgColor = 'bg-red-300';}
if($status === 'blockedUser'){$bgColor = 'bg-orange-300';}
if($status === 'suspension'){$bgColor = 'bg-yellow-400';}
if($status === 'not_member'){$bgColor = 'bg-gray-400';}
if($status === 'isNotJoinFreeEnabled'){$bgColor = 'bg-green-400';}
if($status === 'role-access-error'){$bgColor = 'bg-pink-400';}
@endphp

@if (session($status))
<div class="{{ $bgColor }} w-1/2 mx-auto m-5 p-2 text-center rounded-2xl font-bold text-white">
  {{ session($status) }}
</div>
@endif