<?php

namespace App\Admin\Controllers;

use App\Transfer;
use App\Serials;
use App\Stock;
use App\Product;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\ModelForm;

use PhpOffice\PhpWord\ComplexType\FootnoteProperties;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\Style\Font;

class DocumentController extends Controller
{
    use ModelForm;


    public function purchase($id)
    {
        $t = Transfer::find($id);
        $filename = '采购单'.$id;
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $header = array('name' => '雅黑', 'size' => 32, 'color' => '1B2232', 'alignment' =>\PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $txt = array('name' => '雅黑', 'size' => 12, 'color' => '1B2232', 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $section->addText('采购单(验收)记录单',$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table = $section->addTable('title');
        $vendor = optional($t->products->first()->vendor);
        $table->addRow();
        $table->addCell(5000)->addText("生产企业(供应商)名称:".$vendor->corp);
        $table->addCell(7000)->addText("收货(购货)日期:".$t->ship_at);
        $table->addRow();
        $table->addCell(5000)->addText("联系人:".$vendor->name);
        $table->addCell(7000)->addText("联系方式:".$vendor->mobile);

        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableCellStyle = array('valign' => 'center');
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
        $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
        $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

        $table = $section->addTable('content');

        $table->addRow();
        $table->addCell(500)->addText('序号');
        $table->addCell(500)->addText('名称');
        $table->addCell(3500)->addText('规格');
        $table->addCell(3500)->addText('配置');
        $table->addCell(1500)->addText('物料编码');
        $table->addCell(2500)->addText('注册证号');
        $table->addCell(1500)->addText('生产日期');
        $table->addCell(500)->addText('数量');
        $table->addCell(1500)->addText('单价');
        $table->addCell(1500)->addText('金额');
        $table->addCell(5500)->addText('序列号');
        $i = 1;
        $totalsum = 0;
        foreach ($t->products as $p)
        {
            $ss = Serials::where('purchase_id',$id)->where('product_id',$p->id)->pluck('serial_no');
            $table->addRow();
            $table->addCell(500)->addText($i++);
            $table->addCell(500)->addText($p->name);
            $table->addCell(3500)->addText($p->item);
            $table->addCell(3500)->addText($p->desc?$p->desc:"");
            $table->addCell(1500)->addText($p->sku);
            $table->addCell(2500)->addText($p->cert_no);
            $table->addCell(1500)->addText($t->ship_at);
            $table->addCell(500)->addText($p->pivot->amount);
            $table->addCell(1500)->addText($p->price);
            $table->addCell(1500)->addText($p->pivot->amount*$p->price);
//            $table->addCell(5500)->addText($ss);
            $table->addCell(5500)->addText($this->serialsForHuman($ss));
            $totalsum += $p->pivot->amount*$p->price;
        }
        $table->addRow();
        $table->addcell(3500,$cellColSpan)->addText("验收人:\n".$t->stock->user->name);
        $table->addcell(3500)->addText("验收时间:\n".$t->arrival_at);
        $table->addcell(1500)->addText("合格数:\n");
        $table->addcell(1500)->addText("不合格数:\n");
        $table->addcell(3500,$cellColSpanlong)->addText("不合格处理措施:\n");
        $table->addcell(1500)->addText('合计');
        $table->addcell(2500)->addText($totalsum.'元');
        $table->addcell(3500)->addText("备注: \t".$t->comment);
        $table->addRow();
        $table->addcell(3500,$cellColSpanlong)->addText("库管员:\t".$t->stock->user->name);
        $table->addcell(3500,$cellColSpan)->addText("录入人员:\t".$t->user->name);
        $table->addcell(3500,$cellColSpanlong)->addText('财务:');
        $table->addcell(3500,$cellColSpanlong)->addText('经理:');

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path($filename.'.docx'));
        return response()->download(storage_path($filename.".docx"));

//        return $this->serialsForHuman($ss);
    }

    public function stock($id)
    {
        $ss = Stock::find($id)->amountProducts();

        $filename = Stock::find($id)->name.'库存清单'.Carbon::parse(now())->toDateString();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $header = array('name' => '雅黑', 'size' => 32, 'color' => '1B2232', 'alignment' =>\PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $txt = array('name' => '雅黑', 'size' => 12, 'color' => '1B2232', 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $section->addText($filename,$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableCellStyle = array('valign' => 'center');
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
        $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
        $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

        $table = $section->addTable('content');
        $table->addRow();
        $table->addCell(500)->addText('序号');
        $table->addCell(1500)->addText('名称');
        $table->addCell(1500)->addText('sku');
        $table->addCell(1500)->addText('型号');
        $table->addCell(500)->addText('数量');
        $table->addCell(2500)->addText('序列号');
        foreach ($ss as $k=>$l)
        {
            $p = Product::find($k);

            $sers = Serials::where('stock_id',$id)->where('product_id',$k)->pluck('serial_no');

            $table->addRow();
            $table->addCell(500)->addText($k);
            $table->addCell(1500)->addText($p->name);
            $table->addCell(1500)->addText($p->sku);
            $table->addCell(1500)->addText($p->model);
            $table->addCell(500)->addText($l);
            $table->addCell(2500)->addText($this->serialsForHuman($sers));

        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path($filename.'.docx'));
        return response()->download(storage_path($filename.".docx"));

    }

    public function stock_out($stock_id,$start = "2017-12-31")
    {
        $transfers = Transfer::where('from_stock_id',$stock_id)->where('catalog',3)->where('ship_at','>',$start)->get();
        $filename = '库存出货记录'.$stock_id.$start;
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $header = array('name' => '雅黑', 'size' => 32, 'color' => '1B2232', 'alignment' =>\PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $txt = array('name' => '雅黑', 'size' => 12, 'color' => '1B2232', 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $section->addText('库存出货记录',$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);


        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableCellStyle = array('valign' => 'center');
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
        $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
        $cellColSpanlonglong = array('gridSpan' => 5, 'valign' => 'top');
        $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

        foreach ($transfers as $t)
        {
            $table = $section->addTable('title');
            $table->addRow();
            $table->addCell(5000)->addText("合同编号:".$t->invoiceno);
            $table->addCell(7000)->addText("发货日期:".$t->ship_at);
            $table->addRow();
            $table->addCell(5000)->addText("运单号:".$t->track_id);
            $table->addCell(7000)->addText("备注:".$t->comment);
            $table = $section->addTable('content');

            $table->addRow();
            $table->addCell(500)->addText('序号');
            $table->addCell(500)->addText('名称');
            $table->addCell(3500)->addText('规格');
            $table->addCell(3500)->addText('配置');
            $table->addCell(1500)->addText('物料编码');
            $table->addCell(2500)->addText('注册证号');
            $table->addCell(1500)->addText('生产日期');
            $table->addCell(500)->addText('数量');
            $table->addCell(1500)->addText('单价');
            $table->addCell(1500)->addText('金额');
            $table->addCell(5500)->addText('序列号');
            $i = 1;
            $totalsum = 0;
            foreach ($t->products as $p)
            {
                $ss = Serials::where('purchase_id',$t->id)->where('product_id',$p->id)->pluck('serial_no');
                $table->addRow();
                $table->addCell(500)->addText($i++);
                $table->addCell(500)->addText($p->name);
                $table->addCell(3500)->addText($p->item);
                $table->addCell(3500)->addText($p->desc?$p->desc:"");
                $table->addCell(1500)->addText($p->sku);
                $table->addCell(2500)->addText($p->cert_no);
                $table->addCell(1500)->addText($t->ship_at);
                $table->addCell(500)->addText($p->pivot->amount);
                $table->addCell(1500)->addText($p->price);
                $table->addCell(1500)->addText($p->pivot->amount*$p->price);
//            $table->addCell(5500)->addText($ss);
                $table->addCell(5500)->addText($this->serialsForHuman($ss));
                $totalsum += $p->pivot->amount*$p->price;
            }
            $table->addRow();
//        $table->addcell(3500,$cellColSpan)->addText("验收人:\n".$t->stock2->user->name);
            $table->addcell(3500)->addText("验收时间:\n".$t->arrival_at);
            $table->addcell(1500)->addText("合格数:\n");
            $table->addcell(1500)->addText("不合格数:\n");
            $table->addcell(3500,$cellColSpanlong)->addText("不合格处理措施:\n");
            $table->addcell(1500)->addText('合计');
            $table->addcell(2500)->addText($totalsum.'元');
            $table->addcell(3500,$cellColSpanlong)->addText('备注:');
            $table->addRow();
//        $table->addcell(3500,$cellColSpanlong)->addText("库管员:\t".$t->stock2);
            $table->addcell(3500,$cellColSpanlong)->addText("录入人员:\t".$t->user->name);
            $table->addcell(3500,$cellColSpanlong)->addText('财务:');
            $table->addcell(3500,$cellColSpanlonglong)->addText('经理:');

        }


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path($filename.'.docx'));
        return response()->download(storage_path($filename.".docx"));
    }

    public function stock_in($stock_id,$start = "2017-12-31")
    {
        $transfers = Transfer::where('to_stock_id',$stock_id)->where('catalog',1)->where('ship_at','>',$start)->get();
        $filename = '库存入库记录'.$stock_id.$start;
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $header = array('name' => '雅黑', 'size' => 32, 'color' => '1B2232', 'alignment' =>\PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $txt = array('name' => '雅黑', 'size' => 12, 'color' => '1B2232', 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $section->addText('库存入库记录',$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);


        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableCellStyle = array('valign' => 'center');
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
        $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
        $cellColSpanlonglong = array('gridSpan' => 5, 'valign' => 'top');
        $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

        foreach ($transfers as $t)
        {

            if($p = $t->products->first())
            {
                $section->addText('库存入库记录'.$t->comment,$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                $table = $section->addTable('title');
                if($vendor = $p->vendor)
                {
                    $table->addRow();
                    $table->addCell(5000)->addText("生产企业(供应商)名称:".$vendor->corp);
                    $table->addCell(7000)->addText("收货(购货)日期:".$t->ship_at);
                    $table->addRow();
                    $table->addCell(5000)->addText("联系人:".$vendor->name);
                    $table->addCell(7000)->addText("联系方式:".$vendor->mobile);
                }
                $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
                $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
                $fancyTableStyleName = 'Fancy Table';
                $fancyTableCellStyle = array('valign' => 'center');
                $styleTable = [
                    'borderColor' => '006699',
                    'borderSize' => 6,
                    'cellMargin' => 50,
                ];
                $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
                $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
                $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

                $table = $section->addTable('content');

                $table->addRow();
                $table->addCell(500)->addText('序号');
                $table->addCell(500)->addText('名称');
                $table->addCell(3500)->addText('规格');
                $table->addCell(3500)->addText('配置');
                $table->addCell(1500)->addText('物料编码');
                $table->addCell(2500)->addText('注册证号');
                $table->addCell(1500)->addText('生产日期');
                $table->addCell(500)->addText('数量');
                $table->addCell(1500)->addText('单价');
                $table->addCell(1500)->addText('金额');
                $table->addCell(5500)->addText('序列号');
                $i = 1;
                $totalsum = 0;
                foreach ($t->products as $p)
                {
                    $ss = Serials::where('purchase_id',$t->id)->where('product_id',$p->id)->pluck('serial_no');
                    $table->addRow();
                    $table->addCell(500)->addText($i++);
                    $table->addCell(500)->addText($p->name);
                    $table->addCell(3500)->addText($p->item);
                    $table->addCell(3500)->addText($p->desc?$p->desc:"");
                    $table->addCell(1500)->addText($p->sku);
                    $table->addCell(2500)->addText($p->cert_no);
                    $table->addCell(1500)->addText($t->ship_at);
                    $table->addCell(500)->addText($p->pivot->amount);
                    $table->addCell(1500)->addText($p->price);
                    $table->addCell(1500)->addText($p->pivot->amount*$p->price);
//            $table->addCell(5500)->addText($ss);
                    $table->addCell(5500)->addText($this->serialsForHuman($ss));
                    $totalsum += $p->pivot->amount*$p->price;
                }
                $table->addRow();
                $table->addcell(3500,$cellColSpan)->addText("验收人:\n".$t->stock->user->name);
                $table->addcell(3500)->addText("验收时间:\n".$t->arrival_at);
                $table->addcell(1500)->addText("合格数:\n");
                $table->addcell(1500)->addText("不合格数:\n");
                $table->addcell(3500,$cellColSpanlong)->addText("不合格处理措施:\n");
                $table->addcell(1500)->addText('合计');
                $table->addcell(2500)->addText($totalsum.'元');
                $table->addcell(3500)->addText("备注: \t".$t->comment);
                $table->addRow();
                $table->addcell(3500,$cellColSpanlong)->addText("库管员:\t".$t->stock->user->name);
                $table->addcell(3500,$cellColSpan)->addText("录入人员:\t".$t->user->name);
                $table->addcell(3500,$cellColSpanlong)->addText('财务:');
                $table->addcell(3500,$cellColSpanlong)->addText('经理:');



            }


        }


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path($filename.'.docx'));
        return response()->download(storage_path($filename.".docx"));
    }


    public function ship($id)
    {
        $t = Transfer::find($id);
        $filename = '销售提货单'.$id;
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $header = array('name' => '雅黑', 'size' => 32, 'color' => '1B2232', 'alignment' =>\PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $txt = array('name' => '雅黑', 'size' => 12, 'color' => '1B2232', 'lang' => array('latin' => 'en-US', 'eastAsia' => 'zh-CN'));
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $section->addText('销售提货单',$header,['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $table = $section->addTable('title');
        $table->addRow();
        $table->addCell(5000)->addText("合同编号:".$t->invoiceno);
        $table->addCell(7000)->addText("发货日期:".$t->ship_at);
        $table->addRow();
        $table->addCell(5000)->addText("运单号:".$t->track_id);
        $table->addCell(7000)->addText("备注:".$t->comment);

        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableCellStyle = array('valign' => 'center');
        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50,
        ];
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'top');
        $cellColSpanlong = array('gridSpan' => 3, 'valign' => 'top');
        $cellColSpanlonglong = array('gridSpan' => 5, 'valign' => 'top');
        $phpWord->addTableStyle('content', $fancyTableStyle, $fancyTableFirstRowStyle);

        $table = $section->addTable('content');

        $table->addRow();
        $table->addCell(500)->addText('序号');
        $table->addCell(500)->addText('名称');
        $table->addCell(3500)->addText('规格');
        $table->addCell(3500)->addText('配置');
        $table->addCell(1500)->addText('物料编码');
        $table->addCell(2500)->addText('注册证号');
        $table->addCell(1500)->addText('生产日期');
        $table->addCell(500)->addText('数量');
        $table->addCell(1500)->addText('单价');
        $table->addCell(1500)->addText('金额');
        $table->addCell(5500)->addText('序列号');
        $i = 1;
        $totalsum = 0;
        foreach ($t->products as $p)
        {
            $ss = Serials::where('purchase_id',$id)->where('product_id',$p->id)->pluck('serial_no');
            $table->addRow();
            $table->addCell(500)->addText($i++);
            $table->addCell(500)->addText($p->name);
            $table->addCell(3500)->addText($p->item);
            $table->addCell(3500)->addText($p->desc?$p->desc:"");
            $table->addCell(1500)->addText($p->sku);
            $table->addCell(2500)->addText($p->cert_no);
            $table->addCell(1500)->addText($t->ship_at);
            $table->addCell(500)->addText($p->pivot->amount);
            $table->addCell(1500)->addText($p->price);
            $table->addCell(1500)->addText($p->pivot->amount*$p->price);
//            $table->addCell(5500)->addText($ss);
            $table->addCell(5500)->addText($this->serialsForHuman($ss));
            $totalsum += $p->pivot->amount*$p->price;
        }
        $table->addRow();
//        $table->addcell(3500,$cellColSpan)->addText("验收人:\n".$t->stock2->user->name);
        $table->addcell(3500)->addText("验收时间:\n".$t->arrival_at);
        $table->addcell(1500)->addText("合格数:\n");
        $table->addcell(1500)->addText("不合格数:\n");
        $table->addcell(3500,$cellColSpanlong)->addText("不合格处理措施:\n");
        $table->addcell(1500)->addText('合计');
        $table->addcell(2500)->addText($totalsum.'元');
        $table->addcell(3500,$cellColSpanlong)->addText('备注:');
        $table->addRow();
//        $table->addcell(3500,$cellColSpanlong)->addText("库管员:\t".$t->stock2);
        $table->addcell(3500,$cellColSpanlong)->addText("录入人员:\t".$t->user->name);
        $table->addcell(3500,$cellColSpanlong)->addText('财务:');
        $table->addcell(3500,$cellColSpanlonglong)->addText('经理:');

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save(storage_path($filename.'.docx'));
        return response()->download(storage_path($filename.".docx"));

//        return $this->serialsForHuman($ss);
    }



    private function serialsForHuman($ss)
    {
        $last_id = null;
        $last_item = null;
        $array = [];
        $array_line = [];
        foreach ($ss as $item)
        {
            $item_array = explode('.',$item);
            $id = array_pop($item_array);
            if($id  == $last_id + 1)
            {
                array_push($array_line,$item);
            } else {
                array_push($array,$array_line);
                $array_line = [];
                $array_line[] = $item;
            }
            $last_id = $id;
        }
        $result = "";
        foreach ($array as $i)
        {
            if(count($i)==0)
            {
            }
            elseif (count($i)==1)
            {
                $result .= trim($i[0]).", ";
            }
            else{
                $result .= trim($i[0])."~".trim(end($i)).", ";
            }
        }

        return $result;
    }
}
