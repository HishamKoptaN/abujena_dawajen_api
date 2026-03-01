<?php

if (! function_exists('paginateRes')) {
    function paginateRes(
    $paginator,
    $resource,
    $dataKey = 'data',
    )
    {
        return [
            $dataKey => $resource::collection($paginator->items()),
            'meta' => [
                'total'          => $paginator->total(),
                'total_pages'    => $paginator->lastPage(),
                'current_page'   => $paginator->currentPage(),
                'per_page'       => $paginator->perPage(),
                'has_next_page'  => $paginator->hasMorePages(),
                'has_previous_page' => $paginator->currentPage() > 1,
            ],
        ];
    }
}
