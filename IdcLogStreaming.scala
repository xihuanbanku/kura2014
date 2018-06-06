package com.isinonet.rt

import com.alibaba.fastjson.{JSON, JSONObject}
import kafka.serializer.StringDecoder
import org.apache.log4j.{Level, Logger}
import org.apache.spark.SparkConf
import org.apache.spark.streaming.kafka.KafkaUtils
import org.apache.spark.streaming.{Durations, StreamingContext}

import scala.collection.mutable.ListBuffer

object IdcLogStreaming {

  def main(args: Array[String]): Unit = {
    Logger.getRootLogger.setLevel(Level.WARN)


    val conf: SparkConf = new SparkConf().setMaster("local")
      .setAppName("IdcLogStreaming")
    val ssc = new StreamingContext(conf, Durations.seconds(5))

    val brokers = "docker5:9092,docker6:9092,docker7:9092";
    val topics = "ismartnet.iprobe";
    val topicSet = topics.split(",").toSet
    val kafkaParams = Map[String, String]("metadata.broker.list" -> brokers)
    val messages = KafkaUtils.createDirectStream[String, String, StringDecoder, StringDecoder](ssc, kafkaParams, topicSet)

    val lines = messages.map(_._2).map(JSON.parseObject(_)).filter(_.containsKey("host"))

    val pv = lines.map(x => (x.getString("host"), 1)).reduceByKey(_+_)
    val uv = lines.map(x => (x.getString("sip"), 1)).reduceByKey(_+_).count()


    pv.foreachRDD(_.foreachPartition((it) => {
//        val mapper = session.getMapper(classOf[])
      val list = ListBuffer[Tuple2[String, String]]()
      while (it.hasNext) {
        val unit = it.next()
//        list.+=(("", ""))
      }
//        mapper.insertBatch(list)
//      session.commit
    }))
    ssc.start()
    ssc.awaitTermination()
    ssc.clone()
  }
}
