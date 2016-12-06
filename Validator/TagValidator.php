<?php

namespace Netgen\TagsBundle\Validator;

use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\Validator\Constraints\Tag;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

class TagValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService
     */
    protected $tagsService;

    /**
     * Constructor.
     *
     * @param \Netgen\TagsBundle\API\Repository\TagsService $tagsService
     */
    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$constraint instanceof Tag) {
            throw new UnexpectedTypeException(
                $constraint,
                Tag::class
            );
        }

        try {
            $tag = $this->tagsService->sudo(
                function (TagsService $tagsService) use ($value) {
                    return $tagsService->loadTag($value);
                }
            );

            if ($tag->isSynonym()) {
                $this->context->buildViolation($constraint->synonymMessage)
                    ->addViolation();
            }
        } catch (NotFoundException $e) {
            /** @var \Netgen\TagsBundle\Validator\Constraints\Tag $constraint */
            $this->context->buildViolation($constraint->message)
                ->setParameter('%tagId%', $value)
                ->addViolation();
        }
    }
}