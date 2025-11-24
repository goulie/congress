<footer class="app-footer">
    <div class="site-footer-right">
        @if (rand(1, 100) == 100)
            <i class="voyager-rum-1"></i> {{ __('voyager::theme.footer_copyright2') }}
        @else
            <div class="support-float">
                <button class="support-btn" data-toggle="modal" data-target="#flipFlop">
                    <div class="pulse-ring"></div>
                    <i class="bi bi-headset"></i>
                    <div class="support-tooltip" style="color: #fff;">
                        Contact Support
                    </div>
                </a>
            </div>
        @endif
        @php $version = Voyager::getVersion(); @endphp
        {{-- @if (!empty($version))
            - {{ $version }}
        @endif --}}
    </div>
    <!-- The modal -->
    

</footer>
