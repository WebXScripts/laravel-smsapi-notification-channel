<?php

declare(strict_types=1);

namespace WebXScripts\SmsApiNotification;

use DateTimeInterface;

final class SmsApiMessage
{
    public function __construct(
        public readonly string $content,
        public ?string $from = null,
        public string $encoding = 'utf-8',
        public ?bool $test = null,
        public ?bool $fast = null,
        public ?bool $normalize = null,
        public ?bool $noUnicode = null,
        public ?bool $single = null,
        public ?string $notifyUrl = null,
        public ?DateTimeInterface $expirationDate = null,
        public ?string $timeRestriction = null,
        public ?string $partnerId = null,
        public ?bool $checkIdx = null,
        public ?array $idx = null,
        public ?string $template = null,
        public ?string $param1 = null,
        public ?string $param2 = null,
        public ?string $param3 = null,
        public ?string $param4 = null,
    ) {}

    public static function create(string $content): self
    {
        return new self($content);
    }

    public function from(string $from): self
    {
        return $this->withProperty('from', $from);
    }

    public function encoding(string $encoding): self
    {
        return $this->withProperty('encoding', $encoding);
    }

    public function test(bool $test = true): self
    {
        return $this->withProperty('test', $test);
    }

    public function fast(bool $fast = true): self
    {
        return $this->withProperty('fast', $fast);
    }

    public function normalize(bool $normalize = true): self
    {
        return $this->withProperty('normalize', $normalize);
    }

    public function noUnicode(bool $noUnicode = true): self
    {
        return $this->withProperty('noUnicode', $noUnicode);
    }

    public function single(bool $single = true): self
    {
        return $this->withProperty('single', $single);
    }

    public function notifyUrl(string $url): self
    {
        return $this->withProperty('notifyUrl', $url);
    }

    public function expirationDate(DateTimeInterface $date): self
    {
        return $this->withProperty('expirationDate', $date);
    }

    public function timeRestriction(string $restriction): self
    {
        return $this->withProperty('timeRestriction', $restriction);
    }

    public function partnerId(string $partnerId): self
    {
        return $this->withProperty('partnerId', $partnerId);
    }

    public function checkIdx(bool $checkIdx = true): self
    {
        return $this->withProperty('checkIdx', $checkIdx);
    }

    public function idx(array $idx): self
    {
        return $this->withProperty('idx', $idx);
    }

    public function template(string $template): self
    {
        return $this->withProperty('template', $template);
    }

    public function param1(string $param): self
    {
        return $this->withProperty('param1', $param);
    }

    public function param2(string $param): self
    {
        return $this->withProperty('param2', $param);
    }

    public function param3(string $param): self
    {
        return $this->withProperty('param3', $param);
    }

    public function param4(string $param): self
    {
        return $this->withProperty('param4', $param);
    }

    private function withProperty(string $property, mixed $value): self
    {
        $args = [
            'content' => $this->content,
            'from' => $this->from,
            'encoding' => $this->encoding,
            'test' => $this->test,
            'fast' => $this->fast,
            'normalize' => $this->normalize,
            'noUnicode' => $this->noUnicode,
            'single' => $this->single,
            'notifyUrl' => $this->notifyUrl,
            'expirationDate' => $this->expirationDate,
            'timeRestriction' => $this->timeRestriction,
            'partnerId' => $this->partnerId,
            'checkIdx' => $this->checkIdx,
            'idx' => $this->idx,
            'template' => $this->template,
            'param1' => $this->param1,
            'param2' => $this->param2,
            'param3' => $this->param3,
            'param4' => $this->param4,
        ];

        $args[$property] = $value;

        return new self(...$args);
    }
}