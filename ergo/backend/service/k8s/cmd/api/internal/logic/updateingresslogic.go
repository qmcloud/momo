package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	//exv1beta "k8s.io/api/extensions/v1beta1"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type UpdateIngressLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewUpdateIngressLogic(ctx context.Context, svcCtx *svc.ServiceContext) UpdateIngressLogic {
	return UpdateIngressLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *UpdateIngressLogic) UpdateIngress(req types.RequestCreateIngress) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	getIngress, err := clientSet.ExtensionsV1beta1().Ingresses(req.Namespace).Get(context.TODO(), req.Name, metav1.GetOptions{})

	getIngress.Spec.Rules[0].Host = req.Host

	update, err := clientSet.ExtensionsV1beta1().Ingresses(req.Namespace).Update(context.TODO(), getIngress, metav1.UpdateOptions{})
	if err != nil {
		return nil, errorx.NewCodeError(201, "更新失败", err)
	}
	return nil, errorx.NewCodeError(200, "成功", update)
}
