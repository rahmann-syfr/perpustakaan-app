@props(['paginator'])

@if ($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-4']) }}>
        {{ $paginator->links('vendor.pagination.neo-brutalist') }}
    </div>
@endif
