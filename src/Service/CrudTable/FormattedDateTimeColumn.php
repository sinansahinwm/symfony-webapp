<?php namespace App\Service\CrudTable;

use Omines\DataTablesBundle\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormattedDateTimeColumn extends AbstractColumn
{
    const DEFAULT_FORMAT = 'd.m.Y H:i';

    public function normalize(mixed $value): mixed
    {
        if (null === $value) {
            return $this->options['nullValue'];
        }

        if (!$value instanceof \DateTimeInterface) {
            if (!empty($this->options['createFromFormat'])) {
                $value = \DateTime::createFromFormat($this->options['createFromFormat'], (string)$value);
                if (false === $value) {
                    $errors = \DateTime::getLastErrors();
                    throw new \RuntimeException($errors ? implode(', ', $errors['errors'] ?: $errors['warnings']) : 'DateTime conversion failed for unknown reasons');
                }
            } else {
                $value = new \DateTime((string)$value);
            }
        }

        return $value->format($this->options['format']);
    }

    protected function configureOptions(OptionsResolver $resolver): static
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'createFromFormat' => '',
                'format' => self::DEFAULT_FORMAT,
                'nullValue' => '',
            ])
            ->setAllowedTypes('createFromFormat', 'string')
            ->setAllowedTypes('format', 'string')
            ->setAllowedTypes('nullValue', 'string');

        return $this;
    }
}
