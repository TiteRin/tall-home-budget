<footer class="footer footer-center p-10 bg-base-200 text-base-content rounded border-t border-base-300">
    <nav class="grid grid-flow-col gap-4">
        <a href="{{ route('mentions-legales') }}" class="link link-hover">Mentions légales</a>
        <a href="{{ route('cgu') }}" class="link link-hover">CGU</a>
        <a href="{{ route('confidentialite') }}" class="link link-hover">Politique de confidentialité</a>
    </nav>
    <aside>
        <p>© {{ date('Y') }} - {{ config('app.name') }}</p>
    </aside>
</footer>
