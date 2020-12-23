<?php

namespace App\Validator;

use App\Consts;
use App\Filter\MandateFilter;
use App\Repository\CompetenceCategoryRepository;
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
    private CompetenceCategoryRepository $categoryRepository;

    public function __construct(
        ValidatorInterface $validator,
        CompetenceCategoryRepository $categoryRepository
    )
    {
        $this->validator = $validator;
        $this->categoryRepository = $categoryRepository;
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
                    MandateFilter::QUERY_FROM_DATE => new Assert\Optional(new Assert\Sequentially([
                        new Assert\Type(Type::BUILTIN_TYPE_STRING),
                        new Assert\Regex(Consts::DATE_FILTER_FORMAT_REGEX),
                    ])),
                    MandateFilter::QUERY_FROM_DATE => new Assert\Optional(new Assert\Sequentially([
                        new Assert\Type(Type::BUILTIN_TYPE_STRING),
                        new Assert\Regex(Consts::DATE_FILTER_FORMAT_REGEX),
                    ])),
                    MandateFilter::QUERY_CATEGORY => new Assert\Optional(new Assert\Sequentially([
                        new Assert\Type(['type' => [Type::BUILTIN_TYPE_STRING, Type::BUILTIN_TYPE_ARRAY]]),
                        new Assert\Callback(function ($slugs, ExecutionContextInterface $context) {
                            foreach ((array) $slugs as $slug) {
                                if ($slug && !$this->categoryRepository->findOneBy(['slug' => $slug])) {
                                    $context
                                        ->buildViolation(Consts::VALIDATION_MESSAGE_INVALID_VALUE)
                                        ->addViolation();
                                }
                            }
                        }),
                    ])),
                ],
            ]),
            new Assert\Callback(function (array $query, ExecutionContextInterface $context) {
                if (
                    !isset($query[MandateFilter::QUERY_FROM_DATE]) ||
                    !isset($query[MandateFilter::QUERY_TO_DATE])
                ) {
                    return;
                }

                $fromDate = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[MandateFilter::QUERY_FROM_DATE]);
                $toDate = \DateTime::createFromFormat(Consts::DATE_FORMAT_PHP, $query[MandateFilter::QUERY_TO_DATE]);

                if ($fromDate && $toDate && $fromDate >= $toDate) {
                    $context->buildViolation(Consts::VALIDATION_MESSAGE_INVALID_VALUE)
                        ->atPath('[' . MandateFilter::QUERY_TO_DATE . ']')
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
