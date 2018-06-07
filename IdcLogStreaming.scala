package com.isinonet.ismartnet

import java.util.Properties

import com.isinonet.ismartnet.beans.IdcDaily
import com.isinonet.ismartnet.mapper.IdcDailyMapper
import com.isinonet.ismartnet.utils.{JDBCHelper, PropUtils}
import org.apache.spark.sql.{Encoders, SparkSession}
import org.apache.spark.sql.functions._

import scala.collection.mutable.ListBuffer

/**
  * idc日志分析
  * Created by Administrator on 2018-06-06.
  * 2018-06-06
  */
object LogStats {


  def main(args: Array[String]): Unit = {

    val sparkSession = SparkSession.builder()
      .config("spark.sql.shuffle.partitions", "10")
      .master("local[*]").appName("LogStats").getOrCreate()

    //导入spark的隐式转换
    import sparkSession.implicits._
    //scala  转为java 的集合
    val date = args(0)
    //读取日志文件
    val logToday = sparkSession.read.json("hdfs://192.168.1.200:9000/lhk/" + date + "-11")


//    logToday.filter($"host" =!= "")
//      .select($"host").distinct().rdd.foreachPartition((it) => {
//        val session = JDBCHelper.getSession
//        val mapper = session.getMapper(classOf[WebsiteMapper])
//        var i = 0
//        val list = ListBuffer[Website]()
//        while (it.hasNext) {
//          val row = it.next()
//
//          val unit = new Website
//          unit.setDomain(row.getString(0))
//
//          list+=(unit)
//          println(i)
//          i += 1
//        }
//      mapper.insertBatch(list)
//      session.commit
//    })
    val props: Properties = PropUtils.loadProps("jdbc.properties")
    //读取ip RDD
    val tb_static_ip = sparkSession.read.jdbc(props.getProperty("url"),
      "tb_static_ip",
      props).limit(1000).collect()
    //广播
    val b_tb_static_ip = sparkSession.sparkContext.broadcast(tb_static_ip)
    //读取ua RDD
    val tb_static_uatype = sparkSession.read.jdbc(props.getProperty("url"),
      "tb_static_uatype",
      props)
    //广播
    val b_tb_static_uatype = sparkSession.sparkContext.broadcast(tb_static_uatype)
    //读取website RDD
    val tb_idc_website = sparkSession.read.jdbc(props.getProperty("url"),
      "tb_idc_website",
      props).select($"website_id", $"domain")
    //广播
    val b_tb_idc_website = sparkSession.sparkContext.broadcast(tb_idc_website)

    //统计pv, uv
    val pv_uv = logToday.filter($"host" =!= "")
      .select($"host", $"sip", $"ua")
//      .groupBy($"host", $"ua")
//      .agg(count($"sip").as("pv"), countDistinct($"sip").as("uv"), countDistinct($"ua").as("ua"))

//    pv_uv.show(1000, false)

    val pv_uv_ua_website = pv_uv.join(b_tb_static_uatype.value, $"ua" === $"p_type", "left")
      .join(b_tb_idc_website.value, $"host" === $"domain", "left")

    //确定ip归属地
    pv_uv_ua_website.mapPartitions((it) => {
      val ipDB = b_tb_static_ip.value
      val list = ListBuffer[IdcDaily]()

      val session = JDBCHelper.getSession
      val mapper = session.getMapper(classOf[IdcDailyMapper])
      while (it.hasNext) {
        val e = new IdcDaily
        val row = it.next()
        val sip = row.getLong(1)

        //查找对应的IP归属地
        ipDB.filter(row => sip >= row.getLong(1)&& sip <= row.getLong(2)).take(1).foreach(x=>{
          e.setProvinceId(x.getInt(0).toShort)
          e.setIsp(x.getLong(1).toString)
        })
        e.setWebsiteId(row.getInt(8))
        e.setIsMobile(row.getShort(5))

        list.+=(e)

      }
      list.toIterator
    })(Encoders.bean(classOf[IdcDaily])).toDF().groupBy($"isMobile", $"isp", $"provinceId", $"websiteId")
      .agg(countDistinct($"sip").as("uv"), count($"sip").as("pv")).show(false)
    sparkSession.stop()
  }
}
