<?php

namespace App\Validator;

use App\Consts;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MandateQueryFiltersValidator extends ConstraintValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($query, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\MandateQueryFilters */

        if (empty($query)) {
            return;
        }

        $violations = $this->validator->validate($query, new Assert\Sequentially([
            new Assert\Collection([
                'allowExtraFields' => true,
                'fields' => [
                    Consts::QUERY_FILTER_FROM_DATE => new Assert\Optional([
                        new Assert\Type(Type::BUILTIN_TYPE_STRING),
                        new Assert\Regex(Consts::DATE_FILTER_FORMAT_REGEX),
                    ]),
                    Consts::QUERY_FILTER_FROM_DATE => new Assert\Optional([
                        new Assert\Type(Type::BUILTIN_TYPE_STRING),
                        new Assert\Regex(Consts::DATE_FILTER_FORMAT_REGEX),
                    ]),
                ],
            ]),
            new Assert\Callback(function (array $query, ExecutionContextInterface $context) {
                if (!isset($query[Consts::QUERY_FILTER_FROM_DATE]) || !isset($query[Consts::QUERY_FILTER_TO_DATE])) {
                    return;
                }

                $fromDate = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[Consts::QUERY_FILTER_FROM_DATE]);
                $toDate = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[Consts::QUERY_FILTER_TO_DATE]);

                if ($fromDate && $toDate && $fromDate >= $toDate) {
                    $context->buildViolation('Invalid date')
                        ->atPath('[' . Consts::QUERY_FILTER_TO_DATE . ']')
                        ->addViolation();
                }
            })
        ]));

        if ($violations->count()) {
            foreach ($violations as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $this->context->buildViolation($violation->getMessage())
                    ->atPath($violation->getPropertyPath())
                    ->addViolation();
            }
        }
    }
}
