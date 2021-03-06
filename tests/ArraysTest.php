<?php

use PHPUnit\Framework\TestCase;
use Cajudev\Arrays;
use Cajudev\Strings;

class ArraysTest extends TestCase
{
    public function test_creating_object() 
    {
        $arrays = new Arrays();
        self::assertInstanceOf(Arrays::class, $arrays);
    }

    public function test_creating_from_array()
    {
        $regularArray = ['lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit'];
        $arrays = new Arrays($regularArray);
        self::assertEquals($regularArray, $arrays->get());
    }

    public function test_creating_from_object()
    {
        $object = new class {
            private   $private = 'lorem';
            public    $public = 'ipsum';
            protected $protected = 'dolor';
        };
        $arrays = Arrays::fromObject($object);
        $expect = ['private' => 'lorem', 'public' => 'ipsum', 'protected' => 'dolor'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_creating_from_object_should_return_false()
    {
        $arrays = Arrays::fromObject([1, 2, 3]);
        self::assertNull($arrays);
    }

    public function test_pushing_several_values()
    {
        $arrays = new Arrays();
        $array = ['amet' => 'consectetur'];
        $arrays->push('lorem', $array, 'ipsum', 2222);
        $expect = ['lorem', ['amet' => 'consectetur'], 'ipsum', 2222];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_unshifting_several_values()
    {
        $arrays = new Arrays(['ipsum', 2222]);
        $arrays->unshift('lorem', ['amet' => 'consectetur']);
        $expect = ['lorem', ['amet' => 'consectetur'], 'ipsum', 2222];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_shift()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->shift();
        $expect = ['ipsum', 'dolor'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_pop()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->pop();
        $expect = ['lorem', 'ipsum'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_getting_values_using_successive_get()
    {
        $arrays = new Arrays(['lorem' => ['ipsum' => 'dolor'], 'sit' => 'amet']);
        $expect = 'dolor';
        self::assertEquals($expect, $arrays->get('lorem')->get('ipsum'));
    }

    public function test_getting_values_using_several_keys_argument()
    {
        $arrays = new Arrays([
            'lorem' => [
                'ipsum' => [
                    'dolor' => 'sit'
                ]
            ], 'amet'
        ]);
        $expect = 'sit';
        self::assertEquals($expect, $arrays->get('lorem', 'ipsum', 'dolor'));
    }

    public function test_inserting_values_using_array_sintax()
    {
        $arrays = new Arrays();

        $arrays['lorem']         = 'ipsum';
        $arrays[]                = 'dolor';
        $arrays['sit']['amet']   = 'amet';

        $expect = ['lorem' => 'ipsum', 0 => 'dolor', 'sit' => ['amet' => 'amet']];
        
        self::assertEquals($expect, $arrays->get());
    }

    public function test_accessing_invalid_keys_should_return_null()
    {
        $arrays = new Arrays();
        self::assertEquals(null, $arrays['ipsum']);
    }

    public function test_manipulating_values_using_dot_notation()
    {
        $arrays = new Arrays();

        $arrays['lorem.ipsum.dolor.sit'] = 'amet';
        $arrays['lorem.ipsum.dolor'][] = 'consectetur';
        $arrays['lorem.ipsum.dolor'][] = ['sit' => 'amet'];
        $arrays['lorem.ipsum'][] = $arrays['lorem.ipsum.dolor'];

        $expect['lorem']['ipsum']['dolor']['sit'] = 'amet';
        $expect['lorem']['ipsum']['dolor'][] = 'consectetur';
        $expect['lorem']['ipsum']['dolor'][] = ['sit' => 'amet'];
        $expect['lorem']['ipsum'][] = $expect['lorem']['ipsum']['dolor'];
        
        self::assertEquals($expect, $arrays->get());
    }

    public function test_interval_notation_index_key()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect = [2, 3, 4, 5, 6, 7];
        self::assertEquals($expect, $arrays['2:6']->values()->get());
    }

    public function test_interval_notation_associative_key()
    {
        $arrays = new Arrays([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = ['two'   => 2, 'three' => 3, 'four'  => 4, 'five'  => 5, 'six' => 6];
        self::assertEquals($expect, $arrays['2:5']->get());
    }

    public function test_trying_access_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        $arrays['3:4:5'];
    }

    public function test_trying_set_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        $arrays['3:4:5'] = 10;
    }

    public function test_trying_isset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        isset($arrays['3:4:5']);
    }

    public function test_trying_unset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        unset($arrays['3:4:5']);
    }

    public function test_isset_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem']));
        self::assertEquals(true, $array->isset('lorem'));
    }

    public function test_isset_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, isset($array['ipsum']));
        self::assertEquals(false, $array->isset('ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem.ipsum']));
        self::assertEquals(true, $array->isset('lorem.ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, isset($array['lorem.dolor']));
        self::assertEquals(false, $array->isset('lorem.dolor'));
    }

    public function test_noset_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem'));
    }

    public function test_noset_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->noset('ipsum'));
    }

    public function test_noset_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, $array->noset('lorem.dolor'));
    }

    public function test_noset_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem.ipsum'));
    }

    public function test_empty_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->empty('lorem'));
    }

    public function test_empty_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->empty('ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = false;
        self::assertEquals(true, empty($array['lorem.ipsum']));
        self::assertEquals(true, $array->empty('lorem.ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, empty($array['lorem.ipsum']));
        self::assertEquals(false, $array->empty('lorem.ipsum'));
    }

    public function test_filled_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->filled('lorem'));
    }

    public function test_filled_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->filled('ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'lorem';
        self::assertEquals(true, $array->filled('lorem.ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = false;
        self::assertEquals(false, $array->filled('lorem.ipsum'));
    }

    public function test_unset_key_using_dot_notation()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'sit';
        $array->unset('lorem.ipsum');
        self::assertEquals(false, $array->isset('lorem.ipsum'));
    }

    public function test_unset_key_using_dot_notation_and_function()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'sit';
        unset($array['lorem.ipsum']);
        self::assertEquals(false, $array->isset('lorem.ipsum'));
    }

    public function test_iterating_array_foreach()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        foreach ($arrays as $key => $value) {
            self::assertEquals($arrays[$key], $value);
        }
    }

    public function test_iterating_array_each()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        $arrays->each(function($key, $value) use ($arrays) {
            self::assertEquals($arrays[$key], $value);
        });
    }

    public function test_iterating_using_method_for_forward()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        $arrays->for(0, 1, function($key, $value) use ($arrays) {
            self::assertEquals($arrays[$key], $value);
        });
    }

    public function test_iterating_using_method_for_backward()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        $arrays->for($arrays->count() - 1, -1, function($key, $value) use ($arrays) {
            self::assertEquals($arrays[$key], $value);
        });
    }

    public function test_map()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->map(function($key, $value) {
            return [$key, strtoupper($value)];
        });
        $expect = ['LOREM', 'IPSUM', 'DOLOR'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_map_only_key()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->map(function($key, $value) {
            return [++$key, $value];
        });
        $expect = [1 => 'lorem', 2 => 'ipsum', 3 => 'dolor'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_isArray_should_return_true()
    {
        self::assertEquals(true, Arrays::isArray([1,2,3,4,5]));
    }

    public function test_isArray_should_return_false()
    {
        self::assertEquals(false, Arrays::isArray('1,2,3,4,5'));
    }

    public function test_chunk()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5]);
        $expect = [0 => [1, 2], 1 => [3, 4], 2 => [5]];
        self::assertEquals($expect, $arrays->chunk(2)->get());
    }

    public function test_getting_keys()
    {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $keys = $arrays->keys();
        $expect = ['three', 'eight', 'two'];
        self::assertSame($expect, $keys->get());
    }

    public function test_getting_values()
    {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $values = $arrays->values();
        $expect = [3, 8, 2];
        self::assertSame($expect, $values->get());
    }

    public function test_join()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6]);
        $join   = $arrays->join('|');
        $expect = '1|2|3|4|5|6';
        self::assertEquals($expect, $join->get());
    }

    public function test_column()
    {
        $arrays = new Arrays([
            'lorem1' => [
                'ipsum' => 'dolor1',
                'sit'   => 'amet1'
            ],
            'lorem2' => [
                'ipsum' => 'dolor2',
                'sit'   => 'amet2'
            ]
        ]);
        $column   = $arrays->column('ipsum');
        $expect = ['dolor1', 'dolor2'];
        self::assertEquals($expect, $column->get());
    }

    public function test_combine()
    {
        $arrays = new Arrays();
        $arrays['KEYS']   = new Arrays(['lorem', 'ipsum']);
        $arrays['VALUES'] = new Arrays(['dolor', 'amet']);
        $arrays = Arrays::combine($arrays['KEYS'], $arrays['VALUES']);
        $expect = ['lorem' => 'dolor', 'ipsum'=> 'amet'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_count()
    {
        $arrays    = new Arrays([1, 2, 3, 4, 5]);
        $arrays[2] = [1, 2, 3];

        self::assertEquals(5, $arrays->count());
        self::assertEquals(3, $arrays[2]->count());
    }

    public function test_last()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5]);
        self::assertEquals(5, $arrays->last());
    }

    public function test_keyCase()
    {
        $arrays = new Arrays(['Hello' => 5]);
        
        $arrays->lower();
        self::assertEquals(['hello' => 5], $arrays->get());

        $arrays->upper();
        self::assertEquals(['HELLO' => 5], $arrays->get());
    }

    public function test_toString()
    {
        $arrays = new Arrays(['lorem' => 1, 'Ipsum' => 2]);
        $expect = '{"lorem":1,"Ipsum":2}';
        self::assertEquals($expect, $arrays);
    }

    public function test_length_property()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6]); //6
        unset($arrays[0]); //5
        $arrays->unset(1); //4
        $arrays['lorem.ipsum'] = ['lorem' => 'ipsum']; //5
        $arrays[] = 'dolor'; //6
        $arrays->push(1, 2, 3); //9
        $arrays->shift(); //8
        $arrays->unshift(4, 5, 6); //11
        $arrays->pop(); //10
        $arrays->chunk(2); //5
        self::assertEquals(5, $arrays->length);
    }

    public function test_access_invalid_property_should_return_null()
    {
        $arrays = new Arrays();
        self::assertNull($arrays->lorem);
    }

    public function test_sort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->sort();
        $expect = [2, 3, 8];
        self::assertSame($expect, $arrays->get());
    }

    public function test_rsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->rsort();
        $expect = [8, 3, 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_asort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->asort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $arrays->get());
    }

    public function test_arsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->arsort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_ksort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->ksort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_krsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->krsort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $arrays->get());
    }
}