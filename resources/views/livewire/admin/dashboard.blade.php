
    <div class="grid md:grid-cols-4 gap-3">
        @foreach([
          ['label'=>'Students','value'=>$kpis['students']],
          ['label'=>'Admissions','value'=>$kpis['admissions']],
          ['label'=>'Fee Due','value'=>'₹'.number_format($kpis['due'])],
          ['label'=>'Collected (This Month)','value'=>'₹'.number_format($kpis['collected_m'])],
        ] as $card)
        <div class="bg-white border rounded-2xl p-4">
            <div class="text-xs text-gray-500">{{ $card['label'] }}</div>
            <div class="text-2xl font-semibold mt-1">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

