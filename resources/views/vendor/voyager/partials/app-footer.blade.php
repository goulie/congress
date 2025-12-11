<footer class="app-footer">
    <div class="site-footer-right">
        @if (rand(1, 100) == 100)
            <i class="voyager-rum-1"></i> {{ __('voyager::theme.footer_copyright2') }}
        @else
            <div class="support-float">
                <a class="support-btn" href="mailto:event@afwasa.org">
                    <div class="pulse-ring"></div>
                    <i class="bi bi-envelope"></i>
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
