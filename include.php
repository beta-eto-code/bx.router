<?php

use Psr\Http\Message\StreamInterface;
use BX\Router\PSR7\Stream;
use BX\Router\PSR7\PumpStream;

/**
 * Create a new stream based on the input type.
 *
 * Options is an associative array that can contain the following keys:
 * - metadata: Array of custom metadata.
 * - size: Size of the stream.
 *
 * @param resource|string|int|float|bool|StreamInterface|callable|Iterator|null $resource Entity body data
 * @param array{size?: int, metadata?: array}                                    $options  Additional options
 *
 * @throws InvalidArgumentException if the $resource arg is not valid.
 */
function stream_for($resource = '', array $options = []): StreamInterface
{
    if (is_scalar($resource)) {
        $stream = try_fopen('php://temp', 'r+');
        if ($resource !== '') {
            fwrite($stream, (string) $resource);
            fseek($stream, 0);
        }
        return new Stream($stream, $options);
    }

    switch (gettype($resource)) {
        case 'resource':
            /** @var resource $resource */
            return new Stream($resource, $options);
        case 'object':
            /** @var object $resource */
            if ($resource instanceof StreamInterface) {
                return $resource;
            } elseif ($resource instanceof \Iterator) {
                return new PumpStream(function () use ($resource) {
                    if (!$resource->valid()) {
                        return false;
                    }
                    $result = $resource->current();
                    $resource->next();
                    return $result;
                }, $options);
            } elseif (method_exists($resource, '__toString')) {
                return stream_for((string)$resource, $options);
            }
            break;
        case 'NULL':
            return new Stream(try_fopen('php://temp', 'r+'), $options);
    }

    if (is_callable($resource)) {
        return new PumpStream($resource, $options);
    }

    throw new InvalidArgumentException('Invalid resource type: ' . gettype($resource));
}

function try_fopen(string $filename, string $mode)
{
    $ex = null;
    set_error_handler(static function (int $errno, string $errstr) use ($filename, $mode, &$ex): bool {
        $ex = new \RuntimeException(sprintf(
            'Unable to open %s using mode %s: %s',
            $filename,
            $mode,
            $errstr
        ));
        return false;
    });

    /** @var resource $handle */
    $handle = fopen($filename, $mode);
    restore_error_handler();

    if ($ex) {
        /** @var $ex \RuntimeException */
        throw $ex;
    }

    return $handle;
}

function copy_to_stream(
    StreamInterface $source,
    StreamInterface $dest,
    int $maxLen = -1
): void {
    $bufferSize = 8192;

    if ($maxLen === -1) {
        while (!$source->eof()) {
            if (!$dest->write($source->read($bufferSize))) {
                break;
            }
        }
    } else {
        $remaining = $maxLen;
        while ($remaining > 0 && !$source->eof()) {
            $buf = $source->read(min($bufferSize, $remaining));
            $len = strlen($buf);
            if (!$len) {
                break;
            }
            $remaining -= $len;
            $dest->write($buf);
        }
    }
}


/**
 * Copy the contents of a stream into a string until the given number of
 * bytes have been read.
 *
 * @param StreamInterface $stream Stream to read
 * @param int             $maxLen Maximum number of bytes to read. Pass -1
 *                                to read the entire stream.
 *
 * @throws \RuntimeException on error.
 */
function copy_to_string(StreamInterface $stream, int $maxLen = -1): string
{
    $buffer = '';

    if ($maxLen === -1) {
        while (!$stream->eof()) {
            $buf = $stream->read(1048576);
            if ($buf === '') {
                break;
            }
            $buffer .= $buf;
        }
        return $buffer;
    }

    $len = 0;
    while (!$stream->eof() && $len < $maxLen) {
        $buf = $stream->read($maxLen - $len);
        if ($buf === '') {
            break;
        }
        $buffer .= $buf;
        $len = strlen($buffer);
    }

    return $buffer;
}
