{{-- Activity Feed Items Partial for AJAX Loading --}}
@foreach($activities as $activity)
    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
        <div class="flex items-start space-x-4">
            {{-- Activity Icon --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                {{ $activity->getColorClasses() }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $activity->getIconSvg() !!}
                </svg>
            </div>
            
            {{-- Activity Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                    <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="mt-1 flex items-center space-x-2">
                    {{-- User Info --}}
                    <span class="text-sm text-gray-600">
                        {{ $activity->user ? $activity->user->name : 'Guest' }}
                    </span>
                    
                    {{-- Priority Badge --}}
                    @if(isset($activity->metadata['priority']) && $activity->metadata['priority'] !== 'medium')
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            {{ $activity->metadata['priority'] === 'high' ? 'bg-red-100 text-red-700' : 
                               ($activity->metadata['priority'] === 'low' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($activity->metadata['priority']) }}
                        </span>
                    @endif
                </div>
                
                {{-- Metadata --}}
                @if($activity->metadata && count($activity->metadata) > 0)
                    <div class="mt-2 text-xs text-gray-500">
                        <span>{{ $activity->ip_address }}</span>
                        @if(isset($activity->metadata['location']))
                            <span>
                                • {{ $activity->metadata['location']['city'] ?? '' }}, 
                                {{ $activity->metadata['location']['country'] ?? '' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach

@if($activities->isEmpty())
    <div class="p-8 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Activity Found</h3>
        <p class="text-gray-500">No activities match your current filters.</p>
    </div>
@endif
