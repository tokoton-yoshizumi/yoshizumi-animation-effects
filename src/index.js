const { addFilter } = wp.hooks;
const { createHigherOrderComponent } = wp.compose;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, SelectControl, RangeControl } = wp.components;
const { Fragment } = wp.element;

// すべてのブロックに yaeAosAnimation / yaeAosDuration / yaeAosDelay 属性を追加する
const addAosAttributes = (settings) => {
  if (typeof settings.attributes !== "undefined") {
    settings.attributes = {
      ...settings.attributes,
      yaeAosAnimation: {
        type: "string",
        default: "",
      },
      yaeAosDuration: {
        type: "number",
        default: 800, // デフォルト800ミリ秒
      },
      yaeAosDelay: {
        type: "number",
        default: 0, // デフォルト0ミリ秒
      },
    };
  }
  return settings;
};
addFilter(
  "blocks.registerBlockType",
  "yae/add-aos-attributes",
  addAosAttributes
);

// ブロックエディターにAOS設定UIを追加する
const withAosControls = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    const { attributes, setAttributes, isSelected } = props;
    const { yaeAosAnimation, yaeAosDuration, yaeAosDelay } = attributes;

    return (
      <Fragment>
        <BlockEdit {...props} />
        {isSelected && (
          <InspectorControls>
            <PanelBody title="YAEアニメーション設定" initialOpen={true}>
              <SelectControl
                label="アニメーションタイプ"
                value={yaeAosAnimation}
                options={[
                  { label: "なし", value: "" },
                  { label: "フェードアップ", value: "fade-up" },
                  { label: "フェードダウン", value: "fade-down" },
                  { label: "フェード左", value: "fade-left" },
                  { label: "フェード右", value: "fade-right" },
                  { label: "フェードイン", value: "fade-in" },
                  { label: "フェードアウト", value: "fade-out" },
                  { label: "ズームイン", value: "zoom-in" },
                  { label: "ズームアウト", value: "zoom-out" },
                  { label: "スライドアップ", value: "slide-up" },
                  { label: "スライドダウン", value: "slide-down" },
                  { label: "スライド左", value: "slide-left" },
                  { label: "スライド右", value: "slide-right" },
                  { label: "フリップアップ", value: "flip-up" },
                  { label: "フリップダウン", value: "flip-down" },
                  { label: "フリップ左", value: "flip-left" },
                  { label: "フリップ右", value: "flip-right" },
                ]}
                onChange={(value) => setAttributes({ yaeAosAnimation: value })}
              />

              <RangeControl
                label="アニメーション速度 (ms)"
                value={yaeAosDuration}
                min={100}
                max={3000}
                step={100}
                onChange={(value) => setAttributes({ yaeAosDuration: value })}
              />

              <RangeControl
                label="遅延時間 (ms)"
                value={yaeAosDelay}
                min={0}
                max={3000}
                step={100}
                onChange={(value) => setAttributes({ yaeAosDelay: value })}
              />
            </PanelBody>
          </InspectorControls>
        )}
      </Fragment>
    );
  },
  "withAosControls"
);
addFilter("editor.BlockEdit", "yae/with-aos-controls", withAosControls);

// 保存時に data-aos / data-aos-duration / data-aos-delay を追加する
const saveAosAttributes = (extraProps, blockType, attributes) => {
  const { yaeAosAnimation, yaeAosDuration, yaeAosDelay } = attributes;

  if (yaeAosAnimation) {
    extraProps["data-aos"] = yaeAosAnimation;
  }
  if (yaeAosDuration) {
    extraProps["data-aos-duration"] = yaeAosDuration;
  }
  if (yaeAosDelay) {
    extraProps["data-aos-delay"] = yaeAosDelay;
  }

  return extraProps;
};
addFilter(
  "blocks.getSaveContent.extraProps",
  "yae/save-aos-attributes",
  saveAosAttributes
);
