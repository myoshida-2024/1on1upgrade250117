<!DOCTYPE html>
<html lang="ja">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>AmiVoice API Speech Recognition Sample</title>
    <script type="text/javascript" src="./scripts/opus-encoder-wrapper.js"></script>
    <script type="text/javascript" src="./scripts/ami-asynchrp.js"></script>
    <script type="text/javascript" src="./scripts/ami-easy-hrp.js"></script>
    <script type="text/javascript" src="./lib/wrp/recorder.js"></script>
    <script type="text/javascript" src="./lib/wrp/wrp.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/0.6.1/p5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/0.6.1/addons/p5.dom.min.js"></script>
    <script src="rect.js"></script>
    <link rel="icon" href="/forest-people.sakura.ne.jp/favicon.ico" type="image/x-icon">
    <style>
        * {
            font-family: 'Hiragino Kaku Gothic ProN', 'Helvetica', 'Verdana', 'Lucida Grande', 'ヒラギノ角ゴ ProN', sans-serif;
        }

        a {
            margin-right: 5px;
        }

        button {
            margin: 5px;
        }

        video {
            background: black;
            width: 600px;
        }
    </style>
</head>
<?php
session_start();
// echo "index.php セッションID: " . session_id();

$username = $_SESSION["username"] ;
$lid = $_SESSION["lid"] ; 

// ログイン中のユーザー情報を表示
// echo htmlspecialchars($lid) . "さん、ようこそ！";
?>

<?php
// Composer のオートローダーを読み込む
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// .envがあるディレクトリパスを指定（例: __DIR__ が .env と同じ階層ならこれでOK）
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$appKey = $_ENV['APPKEY'] ?? '';

// これ以降 $_ENV['APPKEY'] で値を取得可能
?>
<body>
    <table>
        <tbody>
                        <tr>
                <td><label for="audioFile">音声ファイル</label></td>
                <td><input id="audioFile" type="file" accept=".wav,.mp3,.flac,.opus,.m4a,.mp4,.webm"></td>
            </tr>
            <tr>
                <td><span>音声ファイルのオーディオ情報</span></td>
                <td><span id="audioInfo"></span></td>
            </tr>
            <!-- <tr> -->
                <!-- <td><label for="engineMode">接続エンジン</label></td> -->
                <!-- <td> -->
                    <!-- <select name="engineMode" id="engineMode"> -->
                        <!-- <option value="-a-general">会話_汎用</option> -->
                        <!-- <option value="-a-general-input">音声入力_汎用</option> -->
                        <!-- <option value="-a-medgeneral">会話_医療</option> -->
                        <!-- <option value="-a-medgeneral-input">音声入力_医療</option> -->
                        <!-- <option value="-a-bizmrreport">会話_製薬</option> -->
                        <!-- <option value="-a-bizmrreport-input">音声入力_製薬</option> -->
                        <!-- <option value="-a-medkarte-input">音声入力_電子カルテ</option> -->
                        <!-- <option value="-a-bizinsurance">会話_保険</option> -->
                        <!-- <option value="-a-bizinsurance-input">音声入力_保険</option> -->
                        <!-- <option value="-a-bizfinance">会話_金融</option> -->
                        <!-- <option value="-a-bizfinance-input">音声入力_金融</option> -->
                        <!-- <option value="-a-general-en">英語_汎用</option> -->
                        <!-- <option value="-a-general-zh">中国語_汎用</option> -->
                    <!-- </select> -->
                <!-- </td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="loggingOptOut">サービス向上のための音声と認識結果の提供を行わない(ログ保存なし)</label></td> -->
                <!-- <td><input type="checkbox" id="loggingOptOut" checked></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="keepFillerToken">フィラー単語(言い淀み)を認識結果に含める</label></td> -->
                <!-- <td><input type="checkbox" id="keepFillerToken"></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="speakerDiarization">話者ダイアライゼーションを有効にする</label></td> -->
                <!-- <td><input type="checkbox" id="speakerDiarization" checked></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="sentimentAnalysis">感情解析を有効にする(非同期HTTP音声認識APIのみ)</label></td> -->
                <!-- <td><input type="checkbox" id="sentimentAnalysis" checked></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="profileWords">ユーザー登録単語</label></td> -->
                <!-- <td><input type="text" id="profileWords" -->
                        <!-- title="{表記1}{半角スペース}{読み1}|{表記2}{半角スペース}{読み2}のように指定します。例:AmiVoice あみぼいす|猫 きかい"></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="useUserMedia">マイクの音声を認識する(WebSocket音声認識API用)</label></td> -->
                <!-- <td><input type="checkbox" id="useUserMedia" checked></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="useDisplayMedia">システムの音声を認識する(WebSocket音声認識API用)</label></td> -->
                <!-- <td><input type="checkbox" id="useDisplayMedia"></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td><label for="useOpusRecorder">音声データをサーバーに送信する前にOgg Opus形式に圧縮する</label></td> -->
                <!-- <td><input type="checkbox" id="useOpusRecorder"></td> -->
            <!-- </tr> -->
            <!-- <tr> -->
                <!-- <td colspan="2"> -->
                    <!-- <div style="font-size: smaller; margin-left: 20px;"> -->
                        <!-- <div>Ogg Opus形式への圧縮には下記のプログラムを使用しています。</div> -->
                        <!-- <div> -->
                            <!-- Opus Recorder License (MIT)<br> -->
                            <!-- Original Work Copyright © 2013 Matt Diamond<br> -->
                            <!-- Modified Work Copyright © 2014 Christopher Rudmin<br> -->
                            <!-- <a href="https://github.com/chris-rudmin/opus-recorder/blob/v8.0.5/LICENSE.md" -->
                            <!-- <a href="./LICENSE.md" -->
                                <!-- target="_blank" -->
                                <!-- rel="noopener noreferrer">https://github.com/chris-rudmin/opus-recorder/blob/v8.0.5/LICENSE.md</a>                            --> --> -->
                                <!-- <a href="./LICENSE.md" -->
                                <!-- target="blank" -->
                                <!-- rel="noopener noreferrer">LICENSE.mdを開く</a>  -->
                        <!-- </div> -->
                    <!-- </div> -->
                <!-- </td> -->
            <!-- </tr> -->
        </tbody>
    </table>
    <div>
        <!-- <a href="player.html" rel="noopener noreferrer" target="_blank">音声プレイヤーを開く</a> -->
        <div>
            <!-- <button id="executeAsyncButton">非同期HTTP音声認識API実行(音声ファイル)</button><br> -->
            <!-- <button id="executeAsyncButton">1 on 1分析開始</button><br> -->
            
            <button id="executeAsyncButton">1 on 1分析開始</button><br>
            <p id="message" style="display: none; color: green;">結果のメールをお待ちください。</p>
            <a href="sendMail.php"></a>

            <script>
              // ボタンを取得
            const button = document.getElementById('executeAsyncButton');
            const message = document.getElementById('message');

            // ボタンクリック時のイベントリスナーを追加
            button.addEventListener('click', function() {
            // メッセージを表示
            message.style.display = 'block';
            });
            </script>

            <!-- <button id="startWrpButton">WebSocket音声認識API開始(マイク or システム)</button> -->
            <!-- <button id="stopWrpButton">WebSocket音声認識API停止</button><br> -->
            <!-- <button id="executeHrpButton">同期HTTP音声認識API実行(短い音声ファイル)</button> -->
        </div>
        <div>
            <textarea id="logs" readonly></textarea>
        </div>
        <script>
            const APP_KEY = "<?php echo htmlspecialchars($appKey, ENT_QUOTES, 'UTF-8'); ?>";
            console.log("137スクリプトが読み込まれました");
            (function () {
                // const appKeyElement = document.getElementById("appKey");
                
                const audioFileElement = document.getElementById("audioFile");
                const audioInfoElement = document.getElementById("audioInfo");
                // const engineModeElement = document.getElementById("engineMode");
                const engineModeElement = "-a-general";
                
                // const loggingOptOutElement = document.getElementById("loggingOptOut");
                // const keepFillerTokenElement = document.getElementById("keepFillerToken");
                // const speakerDiarizationElement = document.getElementById("speakerDiarization");
                // const sentimentAnalysisElement = document.getElementById("sentimentAnalysis");
                const speakerDiarizationElement = true;
                const sentimentAnalysisElement = true;
               // const profileWordsElement = document.getElementById("profileWords");
                // const useUserMediaElement = document.getElementById("useUserMedia");
                // const useDisplayMediaElement = document.getElementById("useDisplayMedia");
                // const useOpusRecorderElement = document.getElementById("useOpusRecorder");
                const useOpusRecorderElement = false;

                const executeAsyncButtonElement = document.getElementById("executeAsyncButton");
                // const startWrpButtonElement = document.getElementById("startWrpButton");
                // const stopWrpButtonElement = document.getElementById("stopWrpButton");
                // const executeHrpButtonElement = document.getElementById("executeHrpButton");

                const logsElement = document.getElementById("logs");

                // 音声ファイルを変更したときの処理
                audioFileElement.addEventListener("change", async function (event) {
                    audioInfoElement.textContent = "";
                    const input = event.target;
                    if (input.files.length === 0) {
                        return;
                    }
                    const selectedFile = input.files[0];
                    if (!(/\.(?:wav|mp3|flac|opus|m4a|mp4|webm)$/i.test(selectedFile.name))) {
                        return;
                    }
                    const audioInfo = await getAudioInfo(selectedFile);
                    if (audioInfo !== null) {
                        audioInfoElement.textContent = audioInfo;
                    }
                });

                /**
                 * 音声ファイルの情報を取得します。
                 * @param {File} audioFile 音声ファイル
                 * @returns 音声ファイルの情報
                 */
                async function getAudioInfo(audioFile) {
                    const getAudioInfo_ = function (audioFile) {
                        return new Promise((resolve, reject) => {
                            if (typeof MediaStreamTrackProcessor !== 'undefined') {
                                const videoElement = document.createElement("video");
                                videoElement.width = 0;
                                videoElement.height = 0;
                                videoElement.volume = 0.01;
                                videoElement.autoplay = true;
                                document.body.appendChild(videoElement);
                                videoElement.onplay = function () {
                                    videoElement.onplay = null;
                                    const stream = videoElement.captureStream();
                                    const audioTrack = stream.getAudioTracks()[0];
                                    const processor = new MediaStreamTrackProcessor({ track: audioTrack });
                                    const processorReader = processor.readable.getReader();
                                    processorReader.read().then(function (result) {
                                        if (result.done) {
                                            return;
                                        }
                                        videoElement.pause();
                                        videoElement.currentTime = 0;
                                        stream.getAudioTracks().forEach((track) => {
                                            track.stop();
                                        });
                                        try {
                                            processorReader.cancel();
                                        } catch (e) { }
                                        const audioDuration = videoElement.duration;
                                        URL.revokeObjectURL(videoElement.src);
                                        videoElement.src = "";
                                        document.body.removeChild(videoElement);
                                        resolve(
                                            audioFile.type + " "
                                            + result.value.sampleRate + "Hz "
                                            + result.value.numberOfChannels + "ch "
                                            + Math.floor(audioDuration) + "sec"
                                        );
                                    });
                                };
                                videoElement.src = URL.createObjectURL(audioFile);
                            } else {
                                resolve(null);
                            }
                        });
                    };
                    return await getAudioInfo_(audioFile);
                }

                // 非同期HTTP音声認識APIの実行
                executeAsyncButtonElement.addEventListener("click", function (event) {
                    if (APP_KEY.length === 0) {
                        alert("APPKEYを入力してください。");
                        return;
                    }
                    if (audioFileElement.files.length === 0) {
                        alert("音声ファイルを選択してください。");
                        return;
                    }
                    const selectedFile = audioFileElement.files[0];
                    if (!(/\.(?:wav|mp3|flac|opus|m4a|mp4|webm)$/i.test(selectedFile.name))) {
                        alert(".wav,.mp3,.flac,.opus,.m4a,.mp4,.webmファイルを選択してください。");
                        return;
                    }
                    addLog("ジョブの登録処理開始。");
                    const asyncHrp = new AsyncHrp();
                    asyncHrp.onProgress = function (message, sessionId) {
                        addLog((sessionId !== null ? "[" + sessionId + "]" : "") + message);
                    };
                    asyncHrp.onError = function (message, sessionId) {
                        addLog((sessionId !== null ? "[" + sessionId + "]" : "") + message);
                    };
                    asyncHrp.onCompleted = function (resultJson, sessionId) {
                        // addLog(resultJson.text);
                        drawResultView(resultJson, selectedFile);
                    };
                    // asyncHrp.engineMode = engineModeElement.value;
                    asyncHrp.engineMode = "-a-general";
                    // asyncHrp.loggingOptOut = loggingOptOutElement.checked;
                    // asyncHrp.keepFillerToken = keepFillerTokenElement.checked;
                    // asyncHrp.speakerDiarization = speakerDiarizationElement.checked;
                    // asyncHrp.sentimentAnalysis = sentimentAnalysisElement.checked;
                    asyncHrp.speakerDiarization = true;
                    asyncHrp.sentimentAnalysis = true;
                    // asyncHrp.profileWords = profileWordsElement.value.trim();

                    postJob(APP_KEY, selectedFile, asyncHrp);
                });

                // 同期HTTP音声認識APIの実行 消す
    
                /**
                 * 非同期/同期 HTTP音声認識API を実行します。
                //  * @param {string} appKey APPKEY
                 * @param {string} appKey $_ENV['APPKEY']
                 * 
                 * @param {File} audioFile 音声ファイル
                 * @param {object} recognizerClient AsyncHrp/EasyHrpオブジェクト
                 */
                function postJob(appKey, audioFile, recognizerClient) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        const audioContext = new AudioContext({ sampleRate: 16000 });
                        // 音声ファイルを32bit リニアPCMに変換
                        audioContext.decodeAudioData(reader.result, async function (audioBuffer) {
                            // モノラルにダウンミックス
                            const OfflineAudioContext = window.OfflineAudioContext || window.webkitOfflineAudioContext;
                            const offlineAudioContext = new OfflineAudioContext(audioBuffer.numberOfChannels, audioBuffer.length, audioBuffer.sampleRate);
                            const merger = offlineAudioContext.createChannelMerger(audioBuffer.numberOfChannels);
                            const source = offlineAudioContext.createBufferSource();
                            source.buffer = audioBuffer;

                            for (let i = 0; i < audioBuffer.numberOfChannels; i++) {
                                source.connect(merger, 0, i);
                            }
                            merger.connect(offlineAudioContext.destination);
                            source.start();

                            const mixedBuffer = await offlineAudioContext.startRendering();
                            const float32PcmData = mixedBuffer.getChannelData(0);

                            merger.disconnect();
                            source.disconnect();
                            audioContext.close();

                            if (useOpusRecorderElement.checked) {
                                // モノラルの32bit リニアPCMをOgg Opusに変換
                                const opusEncoderWrapper = new OpusEncoderWrapper();
                                opusEncoderWrapper.originalSampleRate = audioBuffer.sampleRate;
                                opusEncoderWrapper.useStream = false;
                                opusEncoderWrapper.onCompleted = function (opusData) {
                                    const convertedAudioFile = new Blob([opusData], { type: "audio/ogg; codecs=opus" });
                                    // 音声認識を実行
                                    if (typeof recognizerClient.postJob !== 'undefined') {
                                        recognizerClient.postJob(appKey, convertedAudioFile);
                                    }
                                };
                                await opusEncoderWrapper.initialize();
                                opusEncoderWrapper.start();
                                opusEncoderWrapper.encode(float32PcmData);
                                opusEncoderWrapper.stop();
                            } else {
                                // モノラルの32bit リニアPCMを独自ヘッダ付きのDVI/IMA ADPCMに変換
                                const audioFileConverter = new Worker('./scripts/ami-adpcm-worker.js');
                                audioFileConverter.onmessage = (event) => {
                                    const convertedAudioFile = new Blob([event.data], { type: "application/octet-stream" });
                                    audioFileConverter.terminate();
                                    // 音声認識を実行
                                    if (typeof recognizerClient.postJob !== 'undefined') {
                                        recognizerClient.postJob(appKey, convertedAudioFile);
                                    }
                                };
                                audioFileConverter.postMessage([float32PcmData, audioBuffer.sampleRate], [float32PcmData.buffer]);
                            }
                        }, () => {
                            addLog("Can't decode audio data.");
                        });
                    };
                    reader.readAsArrayBuffer(audioFile);
                }

                // WebSocket音声認識APIの実行 消した

                // WebSocket音声認識APIの停止 消した               

                /**
                 * 認識結果JSONから画面(HTML要素)を構築します
                 * @param {object} resultJson 音声認識結果JSON
                 * @param {object} audioFile 音声ファイル
                 */
                function drawResultView(resultJson, audioFile) {

            //追加コード
            <?php include("funcs.php"); ?>

                    if (!resultJson || !resultJson.segments) {
                        console.error("resultJsonが未定義、または無効です。");
                        return;
                    }
                    
                    // --- ①fetch の Promise を格納する配列を準備 ---
                    const promises = [];
                        
                        console.log("resultJson:", resultJson); // resultJsonの内容を確認
                        
                        // labelごとの経過時間を合計するためのオブジェクト
                        const labelDurations = {};

                        // 話者分析各セグメントをループ処理
                        resultJson.segments.forEach(segment => {
                            segment.results.forEach(result => {
                            result.tokens.forEach(token => {
                            const { label, starttime, endtime } = token;
                                                   
                            sendData(label,starttime, endtime);
                             // 新しく追加 sendData() を呼び出して「Promise」を返すようにして配列にpush
                            promises.push(sendData(label, starttime, endtime));
        
                            // drawRectangle(starttime, y, duration, 10, colorR, colorG, colorB); // (x, y, width, height, colorR, colorG, colorB) 

                        });
                        });
                        });
                        

                        // 各セグメントをループ処理
                        resultJson.sentiment_analysis.segments.forEach(segments => {
                    
                            const { starttime, endtime, energy, stress, concentration } = segments;
                        
                            sendSentData(starttime, endtime, energy, stress, concentration);
                             // 新しく追加 sendSentData() を呼び出して「Promise」を返すようにして配列にpush
                            promises.push(sendSentData(starttime, endtime, energy, stress, concentration));
  
                        });
                       
                 

            // 以下新しく追加 --- ④全fetchが完了したら sendMail.php を呼ぶ ---
            Promise.all(promises)
            .then(() => {
            console.log("すべてのDB書き込みが完了しました。次にメール送信処理を呼び出します。");
            return fetch("sendMail.php", {
                method: "POST",
                // 送るデータがあれば指定
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({ message: "DB書き込み完了" })
            });
            })
            .then(response => response.text()) 
            .then(result => {
                console.log("sendMail.phpのレスポンス:", result);
            // 必要に応じて画面遷移やアラート等の処理
            // window.location.href = "somepage.php";
            })
            .catch(err => {
                console.error("エラー発生:", err);
            });

                      // グラフを書くhtmlにリダイレクト
                        
                        // const encodedData = encodeURIComponent(JSON.stringify(labelDurations));
                        // window.location.href = `graph.html?speaker0=${labelDurations.speaker0}&speaker1=${labelDurations.speaker1}`;

                    const webVttConverter = new Worker('./scripts/ami-webvtt-worker.js');
                    webVttConverter.onmessage = (event) => {
                        const vttSamples = [];
                        vttSamples[0] = event.data;
                        webVttConverter.terminate();

                        // 読み以外の付加情報を取り除いたWEBVTT(スタイル、感情解析の結果、vタグを削除)
                        vttSamples[1] = vttSamples[0].replace(/\nSTYLE[\s\S]+}\n/m, '')
                            .replaceAll(new RegExp('([0-9:.]+ --> [0-9:.]+)(?:.+)', "g"), '$1')
                            .replaceAll(/\n[0-9]+\n[0-9:.]+ --> [0-9:.]+\nENERGY:[0-9]{3} STRESS:[0-9]{3}\n/mg, '')
                            .replaceAll(/<\/?(?:v|[0-9])[^>]*>/g, '');

                        // 読みも取り除いたWEBVTT
                        vttSamples[2] = vttSamples[0].replace(/\nSTYLE[\s\S]+}\n/m, '')
                            .replaceAll(new RegExp('([0-9:.]+ --> [0-9:.]+)(?:.+)', "g"), '$1')
                            .replaceAll(/\n[0-9]+\n[0-9:.]+ --> [0-9:.]+\nENERGY:[0-9]{3} STRESS:[0-9]{3}\n/mg, '')
                            .replaceAll(/<\/?(?:v|[0-9])[^>]*>/g, '')
                            .replaceAll(/<rt>[^<]*<\/rt>/g, '')
                            .replaceAll(/<[^>]*>/g, '');

                        // 読みを取り除かず、読みがあるところは読みだけにしてその前後にスペースを挿入したWEBVTT
                        vttSamples[3] = vttSamples[0].replace(/\nSTYLE[\s\S]+}\n/m, '')
                            .replaceAll(new RegExp('([0-9:.]+ --> [0-9:.]+)(?:.+)', "g"), '$1')
                            .replaceAll(/\n[0-9]+\n[0-9:.]+ --> [0-9:.]+\nENERGY:[0-9]{3} STRESS:[0-9]{3}\n/mg, '')
                            .replaceAll(/<\/?(?:v|[0-9])[^>]*>/g, '')
                            .replaceAll(/<ruby>[^<]*<rt>([^<]*)<\/rt><\/ruby>/g, ' $1 ')
                            .replaceAll(/ {2,}/g, ' ')
                            .replaceAll(/(?: \n|\n )/mg, '\n')
                            .replaceAll(/<[^>]*>/g, '')
                            .replaceAll(/ ([,.?!])/g, '$1');

                        // 認識結果JSONを別ウィンドウで開いたりダウンロードしたりできるようにリンクを作成

                        
                        const links = document.createElement("div");
                        const resultJsonUrl = URL.createObjectURL(
                            new Blob([JSON.stringify(resultJson, null, "\t")], { type: 'application/json;charset=utf-8' }));
                        links.appendChild(createDownloadLink(resultJsonUrl, "JSON", "result.json"));

                        // 認識結果JSONのtextを別ウィンドウで開いたりダウンロードしたりできるようにリンクを作成
                        const linkText = document.createElement("a");
                        let resultText = "";
                        if (typeof resultJson.segments !== 'undefined') {
                            // 非同期HTTP音声認識APIの音声認識結果JSONからsegment毎に改行で区切ったtextを取得
                            resultText = resultJson.segments.map(segment => segment.results[0].text)
                                .filter(value => (typeof value !== 'undefined' && value.length > 0))
                                .join("\n") + "\n";
                        } else {
                            if (Array.isArray(resultJson)) {
                                // WebSocket音声認識APIの音声認識結果JSONを配列にまとめたJSONから発話ごとに改行で区切ったtextを取得
                                resultText = resultJson.map(json => json.text)
                                    .filter(value => (typeof value !== 'undefined' && value.length > 0))
                                    .join("\n") + "\n";
                            } else {
                                // 同期HTTP音声認識APIの音声認識結果JSONからtextを取得
                                resultText = resultJson.text + "\n";
                            }
                        }
                        const resultTextUrl = URL.createObjectURL(
                            new Blob([resultText], { type: 'text/plain;charset=utf-8' }));
                        links.appendChild(createDownloadLink(resultTextUrl, "TEXT", "result.txt"));

                        // 字幕確認用のvideoエレメント作成
                        const player = document.createElement("video");
                        player.src = URL.createObjectURL(audioFile);

                        // videoエレメントの字幕の設定とWebVTTのリンク作成
                        for (let i = 0; i < vttSamples.length; i++) {
                            const track = document.createElement("track");
                            if (i == 0) {
                                track.setAttribute('default', '');
                            }
                            const vttUrl = URL.createObjectURL(
                                new Blob([vttSamples[i]], { type: 'text/vtt;charset=utf-8' }));
                            track.src = vttUrl;
                            track.label = "サンプル" + (i + 1).toString();
                            player.appendChild(track);
                            links.appendChild(createDownloadLink(
                                vttUrl, "WebVTTサンプル" + (i + 1).toString(), "sample" + (i + 1).toString() + ".vtt"));
                        }
                        player.addEventListener("mouseover", function () {
                            this.setAttribute("controls", "");
                        });
                        player.addEventListener("mouseout", function () {
                            this.removeAttribute("controls");
                        });
                        document.body.appendChild(links);
                        document.body.appendChild(player);
                    };
                    webVttConverter.postMessage(resultJson);
                }
               
                       
                /**
                 * ダウンロードリンクを作成します。
                 * @param {string} url URL
                 * @param {string} title タイトル
                 * @param {string} fineName ファイル名
                 * @returns HTMLエレメント
                 */
                function createDownloadLink(url, title, fileName) {
                    const divElement = document.createElement("div");

                    const openLink = document.createElement("a");
                    openLink.href = url;
                    openLink.target = "_blank";
                    openLink.rel = "noopener noreferrer";
                    openLink.textContent = title + "を開く";
                    divElement.appendChild(openLink);

                    const downloadLink = document.createElement("a");
                    downloadLink.href = url;
                    downloadLink.textContent = "ダウンロードする";
                    downloadLink.title = title + "をダウンロード";
                    downloadLink.download = fileName;
                    divElement.appendChild(downloadLink);

                    return divElement;
                }

                /**
                 * ログを出力します。
                 * @param {string} log ログ文字列
                 */
                function addLog(log) {
                    logsElement.textContent += (new Date().toISOString() + " " + log + "\n");
                    setTimeout(function () { logsElement.scrollTop = logsElement.scrollHeight; }, 200);
                }
            })();

            //追加コード
          // sendSentData関数定義
          function sendSentData(starttime, endtime, energy, stress, concentration) {

        const data = new URLSearchParams({
            starttime: starttime,
            endtime: endtime,
            energy: energy,
            stress: stress,
            concentration: concentration
        });

        // 新しく変更 fetch() を return してPromiseを返す
        return fetch("sendSentData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: data
    })
    .then(response => response.text()) //一時的に.text()に変更
    // .then(response => response.json())
    .then(result => {
        // console.log("レスポンス内容：", result); //レスポンス内容の確認
        // if (result.status === "success") {
        if (result.includes("success")) {
            // console.log("sendSentDataデータ送信成功:", result.message);
        } else {
            console.error("エラー:", result.message);
        }
        return result; // 新しく追加 ここでreturnしておくとPromiseチェーンをつなげられる
    })
    .catch(error => {
        console.error("通信エラー:", error);
        throw error; // 新しく追加 エラーを再スローするとPromise.allでcatchできる
    });
}

        
            //追加コード
          // sendData関数定義
          function sendData(label,starttime, endtime) {

        const data = new URLSearchParams({
            label: label,
            starttime: starttime,
         endtime: endtime
         });
        // 新しく変更 fetch() を return してPromiseを返す
        return fetch("sendData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === "success") {
            console.log("sendDataデータ送信成功:", result.message);
        } else {
            console.error("エラー:", result.message);
        }
        return result; // ここでreturnしておくとPromiseチェーンをつなげられる
    })
    .catch(error => {
        console.error("通信エラー:", error);
        throw error; // エラーを再スローするとPromise.allでcatchできる
    });
}



        </script>
</body>

</html>