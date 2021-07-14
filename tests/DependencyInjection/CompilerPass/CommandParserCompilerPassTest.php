<?php

/*
 * This file is part of the PostmanGeneratorBundle package.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DependencyInjection\CompilerPass;

use PostmanGeneratorBundle\DependencyInjection\CompilerPass\CommandParserCompilerPass;
use Symfony\Component\DependencyInjection\Reference;

class CommandParserCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $containerMock = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $definitionMock = $this->prophesize('Symfony\Component\DependencyInjection\Definition');

        $containerMock->getDefinition('postman.command_parser.chain')
            ->willReturn($definitionMock->reveal())
            ->shouldBeCalledTimes(1);
        $containerMock->findTaggedServiceIds('postman.command_parser')
            ->willReturn([
                'postman.command_parser.foo' => [['name' => 'postman.command_parser', 'priority' => 0]],
                'postman.command_parser.bar' => [['name' => 'postman.command_parser', 'priority' => 1]],
            ])
            ->shouldBeCalledTimes(1);
        $containerMock->getDefinition('postman.command_parser.foo')
            ->willReturn($definitionMock->reveal())
            ->shouldBeCalledTimes(1);
        $containerMock->getDefinition('postman.command_parser.bar')
            ->willReturn($definitionMock->reveal())
            ->shouldBeCalledTimes(1);
        $definitionMock->getTag('postman.command_parser')->willReturn(
            ['name' => 'postman.command_parser', 'priority' => 0],
            ['name' => 'postman.command_parser', 'priority' => 1]
        )->shouldBeCalledTimes(2);

        $definitionMock->replaceArgument(0, [
            new Reference('postman.command_parser.bar'),
            new Reference('postman.command_parser.foo'),
        ])->shouldBeCalledTimes(1);

        $compilerPass = new CommandParserCompilerPass();
        $compilerPass->process($containerMock->reveal());
    }
}
