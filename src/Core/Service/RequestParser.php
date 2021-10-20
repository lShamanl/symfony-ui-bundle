<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * UIBundleFoundation
 */
class RequestParser
{
    /**
     * @param Request $request
     * @return array<string, string>
     */
    public function parse(Request $request): array
    {
        $query   = $request->query->all();
        $content = (array) json_decode((string) $request->getContent(), true);
        $requestData = $request->request->all();

        /** @var array<string, string> $payload */
        $payload = array_merge($query, $content, $requestData);
        return $payload;
    }
}
